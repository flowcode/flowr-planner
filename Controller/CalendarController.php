<?php

namespace Flower\PlannerBundle\Controller;

use Doctrine\ORM\QueryBuilder;
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
 * @Route("/calendar")
 */
class CalendarController extends Controller
{

    /**
     * Calendar view
     *
     * @Route("/", name="planner_calendar")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return array(

        );
    }



}
