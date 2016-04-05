<?php

namespace Flower\PlannerBundle\Controller\Api;

use Flower\ModelBundle\Entity\Planner\Event;
use Flower\ModelBundle\Entity\Planner\Reminder;
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
        $account = $em->getRepository('FlowerModelBundle:Planner\Event')->find($id);

        $view = FOSView::create($account, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('api', 'full'));
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
        if ($request->get("id")) {
            $event = $em->getRepository('FlowerModelBundle:Planner\Event')->find($request->get("id"));
        } else {
            $event = new Event();
            $event->setOwner($this->getUser());
            $event->setVisible(0);
        }

        $event->setTitle($request->get("title"));
        $event->setDescription($request->get("description"));
        $event->setStartDate(new \DateTime($request->get("start_date")));
        $event->setEndDate(new \DateTime($request->get("end_date")));

        $reminders = $request->get("reminders", array());
        foreach ($reminders as $reminder) {
            $reminderInDB = null;
            $found = false;
            foreach ($event->getReminders() as $oldReminders) {
                if ($oldReminders->getId() == $reminder['id']) {
                    $found = true;
                }
            }
            if (!$found) {
                $newReminder = new Reminder();
                $newReminder->setAmount($reminder['amount']);
                $newReminder->setType($reminder['type']);
                $newReminder->setUnity($reminder['unity']);
                $newReminder->setUser($this->getUser());
                $newReminder->setDate(new \DateTime());

                $event->addReminder($newReminder);
            }
        }

        $em->persist($event);
        $em->flush();

        $response = array("success" => true, "message" => "Account created", "entity" => $this->getEventArr($event));
        return $this->handleView(FOSView::create($response, Codes::HTTP_OK)->setFormat("json"));
    }


    private function getEventArr($event)
    {
        $eventArr = array(
            "id" => $event->getId(),
            "title" => $event->getDescriptiveTitle(),
            "start" => $event->getStartDate(),
            "end" => $event->getEndDate(),
            "className" => $event->getRelatedEntities(),
            "allDay" => false,
        );
        return $eventArr;
    }

    private function getEventRepresentation($events)
    {
        $eventsArr = array();
        foreach ($events as $event) {
            $eventsArr[] = $this->getEventArr($event);
        }
        return $eventsArr;
    }

}
