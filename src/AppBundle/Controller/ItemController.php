<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AppBundle\Entity\Item;
use AppBundle\Form\ItemType;

/**
 * Item controller.
 *
 * @Route("/item")
 */
class ItemController extends Controller
{

    /**
     * Lists all Item entities.
     *
     * @Route("/", name="item")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AppBundle:Item')->findAll();

        $em = $this->getDoctrine()->getManager();
        $buckets = $em->getRepository('AppBundle:Bucket')->findALl();

        return array(
            'entities' => $entities,
            'buckets'  => $buckets,
        );
    }
    /**
     * Creates a new Item entity.
     *
     * @Route("/", name="item_create")
     * @Method("POST")
     * @Template("AppBundle:Item:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Item();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $session = $request->getSession();
            $bucket = $session->get('bucket');
            $em = $this->getDoctrine()->getManager();
            $bucketEntity = $em->getRepository('AppBundle:Bucket')->find($bucket);
            if (!$bucketEntity) {
                throw $this->createNotFoundException('Unable to find Bucket entity.');
            }

            $user = $this->get('security.context')->getToken()->getUser();
            $entity->setUser($user);
            $entity->setBucket($bucketEntity);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('bucket_show', array('id' => $bucket)));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Item $entity)
    {
        $form = $this->createForm(new ItemType(), $entity, array(
            'action' => $this->generateUrl('item_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', [
            'label' => 'Create Bucket',
            'attr' => [
                'class' => 'btn btn-default'
            ]

        ]);

        return $form;
    }

    /**
     * Displays a form to create a new Item entity.
     *
     * @Route("/new/{bucket}", name="item_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request, $bucket)
    {
        $session = $request->getSession();
        $session->set('bucket', $bucket);

        $entity = new Item();
        $form   = $this->createCreateForm($entity);

        $em = $this->getDoctrine()->getManager();
        $bucketEntity = $em->getRepository('AppBundle:Bucket')->find($bucket);

        // $session->get('name');

        if (!$bucketEntity) {
            throw $this->createNotFoundException('Unable to find the bucket.');
        }

        $user = $this->get('security.context')->getToken()->getUser();
        $allBuckets = $this->getAllBuckets($user);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'bucket' => $bucketEntity,
            'allBuckets' => $allBuckets
        );
    }

    /**
     * Finds and displays a Item entity.
     *
     * @Route("/{id}", name="item_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $em2 = $this->getDoctrine()->getManager();
        $bucket = $em2->getRepository('AppBundle:Bucket')->find($entity->getBucket());

        if (!$bucket) {
            throw $this->createNotFoundException('Unable to find Bucket entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'item'      => $entity,
            'bucket'    => $bucket,
            'delete_form' => $deleteForm->createView(),
            'allBuckets' => $this->getAllBuckets($this->get('security.context')->getToken()->getUser())
        );
    }

    /**
     * Displays a form to edit an existing Item entity.
     *
     * @Route("/{id}/edit", name="item_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $em2 = $this->getDoctrine()->getManager();
        $bucket = $em2->getRepository('AppBundle:Bucket')->find($entity->getBucket());

        if (!$bucket) {
            throw $this->createNotFoundException('Unable to find Bucket entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'bucket'      => $bucket,
            'allBuckets' => $this->getAllBuckets(
                $this->get('security.context')->getToken()->getUser()
            ),
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Item entity.
    *
    * @param Item $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Item $entity)
    {
        $form = $this->createForm(new ItemType(), $entity, array(
            'action' => $this->generateUrl('item_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', [
            'label' => 'Update Item',
            'attr' => [
                'class' => 'btn btn-success'
            ]
        ]);

        return $form;
    }
    /**
     * Edits an existing Item entity.
     *
     * @Route("/{id}", name="item_update")
     * @Method("PUT")
     * @Template("AppBundle:Item:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('item_show', array('id' => $id)));
        }

        return $this->redirect($this->generateUrl('item_show', array('id' => $id)));
    }
    /**
     * Deletes a Item entity.
     *
     * @Route("/{id}", name="item_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Item')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Item entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        $session = $request->getSession();
        $bucket = $session->get('bucket');

        return $this->redirect($this->generateUrl('bucket_show', ['id' => $bucket]));
    }

    /**
     * Creates a form to delete a Item entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('item_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', [
                'label' => 'Delete Item',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ])
            ->getForm()
        ;
    }

    public function getAllBuckets($user) {
        $em = $this->getDoctrine()->getManager();
        $buckets = $em->getRepository('AppBundle:Bucket')->findBy([
            'user' => $user
        ]);

        $return = [];
        $x = 0;
        foreach($buckets as $bucket) {
            $return[$x]['name'] = $bucket->getName();
            $return[$x]['id']   = $bucket->getId();
            $return[$x]['qty_items'] = (int)(count($this->getAllItems($bucket)));
            $x++;
        }

        return $return;
    }

    public function getAllItems($id){
        if (!$this->getUserFromBucket($id)) {
            throw $this->createNotFoundException('This user does not have access to this bucket');
        }

        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('AppBundle:Item')->findBy([
            'bucket' => $id
        ],
            ['id' => 'DESC']
        );
    }

    public function getUserFromBucket($bucketId) {
        $em = $this->getDoctrine()->getManager();
        $bucket = $em->getRepository('AppBundle:Bucket')->find($bucketId);

        if (!$bucket) {
            throw $this->createNotFoundException('This user does not have access to this bucket');
        }

        return $bucket->getUser();
    }
}
