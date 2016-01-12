<?php

namespace Flower\PlannerBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;
use Flower\ClientsBundle\Form\Type\Api\AccountType;

/**
 * Project controller.
 */
class EventController extends FOSRestController
{
    public function getMyAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $events = $em->getRepository('FlowerModelBundle:Planner\Event')->findByStartDate($this->getUser(), new \DateTime('@' . $request->get("start")), new \DateTime('@' . $request->get("end")), 1000, 0);

        $eventsOutput = $this->getEventRepresentation($events);

        $view = FOSView::create($eventsOutput, Codes::HTTP_OK)->setFormat('json');
        return $this->handleView($view);
    }

    public function getAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $events = $em->getRepository('FlowerModelBundle:Planner\Event')->findAllByStartDate(new \DateTime('@' . $request->get("start")), new \DateTime('@' . $request->get("end")), 1000, 0);

        $eventsOutput = $this->getEventRepresentation($events);

        $view = FOSView::create($eventsOutput, Codes::HTTP_OK)->setFormat('json');
        return $this->handleView($view);
    }


    public function getByIdAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository('FlowerModelBundle:Clients\Account')->find($id);

        $view = FOSView::create($account, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('api'));
        return $this->handleView($view);
    }
    public function publicGetAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $accounts = $em->getRepository('FlowerModelBundle:Clients\Account')->findAll();

        $view = FOSView::create($accounts, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    public function publicGetByIdAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository('FlowerModelBundle:Clients\Account')->find($id);

        $view = FOSView::create($account, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }
     public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $account = new Account();
        $form = $this->createForm(new AccountType(), $account);

        $form->submit($request);
        if ($form->isValid()) {
            $em->persist($account);
            $em->flush();

            $response = array("success" => true, "message" => "Account created", "entity" =>$account );
            return $this->handleView(FOSView::create($response, Codes::HTTP_OK)->setFormat("json"));
        }

        $response= array('success' => false, 'errors' => $form->getErrors());
        return $this->handleView(FOSView::create($response, Codes::HTTP_NOT_FOUND)->setFormat("json"));
    }

    private function getEventRepresentation($events){
        $eventsArr = array();
        foreach ($events as $event) {
            $eventsArr[] = array(
                "title" => $event->getTitle(),
                "start" => $event->getStartDate(),
                "end" => $event->getEndDate(),
                "allDay" => false,
            );
        }
        return $eventsArr;
    }

}
