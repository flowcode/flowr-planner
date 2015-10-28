<?php

namespace Flower\PlannerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Planner\EventStatus;
use Flower\PlannerBundle\Form\Type\Planner\EventStatusType;

/**
 * EventStatus controller.
 *
 * @Route("/admin/eventstatus")
 */
class EventStatusController extends Controller
{
    /**
     * Lists all EventStatus entities.
     *
     * @Route("/", name="eventstatus")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Planner\EventStatus')->createQueryBuilder('p');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a EventStatus entity.
     *
     * @Route("/{id}/show", name="eventstatus_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(EventStatus $eventstatus)
    {
        $deleteForm = $this->createDeleteForm($eventstatus->getId(), 'eventstatus_delete');

        return array(
            'eventstatus' => $eventstatus,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new EventStatus entity.
     *
     * @Route("/new", name="eventstatus_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $eventstatus = new EventStatus();
        $form = $this->createForm(new EventStatusType(), $eventstatus);

        return array(
            'eventstatus' => $eventstatus,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new EventStatus entity.
     *
     * @Route("/create", name="eventstatus_create")
     * @Method("POST")
     * @Template("FlowerPlannerBundle:EventStatus:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $eventstatus = new EventStatus();
        $form = $this->createForm(new EventStatusType(), $eventstatus);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($eventstatus);
            $em->flush();

            return $this->redirect($this->generateUrl('eventstatus_show', array('id' => $eventstatus->getId())));
        }

        return array(
            'eventstatus' => $eventstatus,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing EventStatus entity.
     *
     * @Route("/{id}/edit", name="eventstatus_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(EventStatus $eventstatus)
    {
        $editForm = $this->createForm(new EventStatusType(), $eventstatus, array(
            'action' => $this->generateUrl('eventstatus_update', array('id' => $eventstatus->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($eventstatus->getId(), 'eventstatus_delete');

        return array(
            'eventstatus' => $eventstatus,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing EventStatus entity.
     *
     * @Route("/{id}/update", name="eventstatus_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerPlannerBundle:EventStatus:edit.html.twig")
     */
    public function updateAction(EventStatus $eventstatus, Request $request)
    {
        $editForm = $this->createForm(new EventStatusType(), $eventstatus, array(
            'action' => $this->generateUrl('eventstatus_update', array('id' => $eventstatus->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('eventstatus_show', array('id' => $eventstatus->getId())));
        }
        $deleteForm = $this->createDeleteForm($eventstatus->getId(), 'eventstatus_delete');

        return array(
            'eventstatus' => $eventstatus,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a EventStatus entity.
     *
     * @Route("/{id}/delete", name="eventstatus_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(EventStatus $eventstatus, Request $request)
    {
        $form = $this->createDeleteForm($eventstatus->getId(), 'eventstatus_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($eventstatus);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('eventstatus'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
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
