<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Elastica\Query;
use AppBundle\Entity\Bucket;



class SearchController extends Controller
{
    /**
     * @Route("/search", name="search")
     * @Method("GET")
     * @Template("AppBundle:Search:index.html.twig")
     */
    public function searchAction(Request $request)
    {
        $searchKey = $request->query->get('searchKey');
        // $user = $this->get('security.context')->getToken()->getUser();

        $finderBucket = $this->container->get('fos_elastica.index.search.bucket');
        $finderItem = $this->container->get('fos_elastica.index.search.item');

        $buckets = $finderBucket->search($searchKey);
        $items = $finderItem->search($searchKey);

        return [
            'searchKey' => $searchKey,
            'buckets' => $buckets,
            'items' => $items,
            'allBuckets' => $this->getAllBuckets($this->get('security.context')->getToken()->getUser())
        ];
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

    public function getUserFromBucket($bucketId) {
        $em = $this->getDoctrine()->getManager();
        $bucket = $em->getRepository('AppBundle:Bucket')->find($bucketId);

        if (!$bucket) {
            throw $this->createNotFoundException('This user does not have access to this bucket');
        }

        return $bucket->getUser();
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


}
