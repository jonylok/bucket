<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Bucket;
use AppBundle\Form\BucketType;

/**
 * Bucket controller.
 *
 * @Route("/bucket")
 */
class BucketController extends Controller
{

    /**
     * Lists all Bucket entities.
     *
     * @Route("/", name="bucket")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Bucket')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Bucket entity.
     *
     * @Route("/", name="bucket_create")
     * @Method("POST")
     * @Template("AppBundle:Bucket:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Bucket();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('bucket_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Bucket entity.
     *
     * @param Bucket $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Bucket $entity)
    {
        $form = $this->createForm(new BucketType(), $entity, array(
            'action' => $this->generateUrl('bucket_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Bucket entity.
     *
     * @Route("/new", name="bucket_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Bucket();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Bucket entity.
     *
     * @Route("/{id}", name="bucket_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Bucket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bucket entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Bucket entity.
     *
     * @Route("/{id}/edit", name="bucket_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Bucket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bucket entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Bucket entity.
    *
    * @param Bucket $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Bucket $entity)
    {
        $form = $this->createForm(new BucketType(), $entity, array(
            'action' => $this->generateUrl('bucket_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Bucket entity.
     *
     * @Route("/{id}", name="bucket_update")
     * @Method("PUT")
     * @Template("AppBundle:Bucket:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Bucket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Bucket entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('bucket_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Bucket entity.
     *
     * @Route("/{id}", name="bucket_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Bucket')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Bucket entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('bucket'));
    }

    /**
     * Creates a form to delete a Bucket entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bucket_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
