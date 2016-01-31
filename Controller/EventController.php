<?php

namespace Flower\PlannerBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\ModelBundle\Entity\Board\History;
use Flower\ModelBundle\Entity\Planner\Event;
use Flower\ModelBundle\Entity\Planner\Reminder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Genemu\Bundle\FormBundle\Geolocation\AddressGeolocation;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
/**
 * Event controller.
 *
 * @Route("/event")
 */
class EventController extends Controller
{

    /**
     * Lists all Events entities.
     *
     * @Route("/", name="event")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $limit = 10;
        $page = 0;
        $today = new \DateTime();
        $today->sub(new \DateInterval('PT1H'));
        $tomorrow = new \DateTime('tomorrow');
        $today = $em->getRepository('FlowerModelBundle:Planner\Event')->findByStartDate( $this->getUser(),$today,$tomorrow,$limit,$page*$limit);
        $next = $em->getRepository('FlowerModelBundle:Planner\Event')->findByStartDate( $this->getUser(),$tomorrow,null,$limit,$page*$limit);
        return array(
            'today' => $today,
            'next' => $next,
        );
    }

    /**
     * Displays a form to create a new Event entity.
     *
     * @Route("/new", name="event_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $event = new Event();
        $reminder1 = new Reminder();
        $reminder1->setUnity(Reminder::$UNITY_DAY);
        $reminder1->setType(Reminder::$TYPE_EMAIL);
        $reminder1->setAmount(1);
        $event->addReminder($reminder1);
        $reminder2 = new Reminder();
        $reminder2->setUnity(Reminder::$UNITY_MINUTES);
        $reminder2->setType(Reminder::$TYPE_EMAIL);
        $reminder2->setAmount(10);
        $event->addReminder($reminder2);
        $form = $this->createForm($this->get('form.type.event'), $event, array(
            'action' => $this->generateUrl('event_create'),
            'method' => 'POST',
        ));
        return array(
            'event' => $event,
            'form' => $form->createView(),
        );
    }
    private function buildReminders($newEvent, $oldEvent = null){
        $user = $this->getUser();
        foreach ($newEvent->getReminders() as $reminder) {
            $reminder->setUser($user);
        }
        if($oldEvent != null){
            foreach ($oldEvent->getReminders() as $reminder) {
                if($reminder->getUser()->getId() != $user->getId()){
                    $newEvent->addReminder($reminder);
                }
            }
        }
        return $newEvent;
    }
    /**
     * Creates a new Event entity.
     *
     * @Route("/create", name="event_create")
     * @Method("POST")
     * @Template("FlowerPlannerBundle:Event:new.html.twig")
     */
    public function createAction(Request $request)
    {

        $event = new Event();
        $form = $this->createForm($this->get('form.type.event'), $event);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $event = $this->buildReminders($event);
            $event->updateRedminders();
            $event->setOwner($this->getUser());
            $event->setLatitude($event->getAddress()->getLatitude());
            $event->setLongitude($event->getAddress()->getLongitude());
            $event->setAddress($event->getAddress()->getAddress());
            $em->persist($event);
            $em->flush();

            $this->get('board.service.history')->addSimpleUserActivity(History::TYPE_EVENT, $this->getUser(), $event, History::CRUD_CREATE);

            $this->get("flower.core.service.notification")->notificateNewEvent($event);
            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'event_new' : 'event';
            return $this->redirectToRoute($nextAction);
        }

        return array(
            'event' => $event,
            'form' => $form->createView(),
        );
    }

    private function checkPermissions(Event $event){
        $userId = $this->getUser()->getId();
        if($event->getOwner()->getId() != $userId){
            $error = true;
            foreach ($event->getUsers() as $user) {
                if($user->getId() == $userId){
                    $error = false;
                    break;
                }
            }
            if($error){
                throw new AccessDeniedHttpException();
            }
        }
    }
    private function checkOwner(Event $event){
        $userId = $this->getUser()->getId();
        if($event->getOwner()->getId() != $userId){
            throw new AccessDeniedHttpException();
        }
    }
    private function filterReminders(Event $event){
        $user = $this->getUser();
        foreach ($event->getReminders() as $reminder) {
            if($reminder->getUser() != null && $reminder->getUser()->getId() != $user->getId()){
                $event->removeReminder($reminder);
            }
        }
    }
    /**
     * Displays a form to edit an existing Event entity.
     *
     * @Route("/{id}/edit", name="event_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Event $event)
    {
        $this->checkPermissions($event);
        $event->setAddress(array("address"=> $event->getAddress()));
        $this->filterReminders($event);
        //TODO. mejorar este fix barato que es para cuando no tienen reminders
        if(count($event->getReminders()) == 0){
            $newReminder = new Reminder();
            $newReminder->setUnity(Reminder::$UNITY_MINUTES);
            $newReminder->setType(Reminder::$TYPE_EMAIL);
            $newReminder->setAmount(10);
            $event->addReminder($newReminder);
        }
        $editForm = $this->createForm($this->get('form.type.event'), $event, array(
            'action' => $this->generateUrl('event_update', array('id' => $event->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($event->getId(), 'event_delete');

        return array(
            'event' => $event,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Event entity.
     *
     * @Route("/{id}/update", name="event_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerPlannerBundle:Event:edit.html.twig")
     */
    public function updateAction(Event $event, Request $request)
    {
        $oldReminders = clone $event->getReminders();
        $this->checkPermissions($event);
        $event->setAddress(array("address"=> $event->getAddress()));
        $editForm = $this->createForm($this->get('form.type.event'), $event, array(
            'action' => $this->generateUrl('event_update', array('id' => $event->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $oldEvent = new Event();
            $oldEvent->setReminders($oldReminders);
            $event = $this->buildReminders($event,$oldEvent);
            $event->updateRedminders();
            $event->setLatitude($event->getAddress()->getLatitude());
            $event->setLongitude($event->getAddress()->getLongitude());
            $event->setAddress($event->getAddress()->getAddress());
            $em->flush();

            $this->get('board.service.history')->addSimpleUserActivity(History::TYPE_EVENT, $this->getUser(), $event, History::CRUD_UPDATE);

            return $this->redirect($this->generateUrl('event', array('id' => $event->getId())));
        }
        $deleteForm = $this->createDeleteForm($event->getId(), 'event_delete');

        return array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Event entity.
     *
     * @Route("/{id}/delete", name="event_delete", requirements={"id"="\d+"})
     * @Method({"DELETE","GET"})
     */
    public function deleteAction(Event $event, Request $request)
    {
            $this->checkOwner($event);
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
     
        return $this->redirect($this->generateUrl('event'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl($route, array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }


}
