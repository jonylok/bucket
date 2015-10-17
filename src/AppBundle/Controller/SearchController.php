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
        $user = $this->get('security.context')->getToken()->getUser();
        $searchKey = strtolower($request->query->get('searchKey'));

        // $user = $this->get('security.context')->getToken()->getUser();

        $finderBucket = $this->container->get('fos_elastica.index.search.bucket');
        $finderItem = $this->container->get('fos_elastica.index.search.item');

        $boolQuery1 = new \Elastica\Query\BoolQuery();
        $terms1 = new \Elastica\Query\Terms();
        $terms1->setTerms('name', array($searchKey));
        $boolQuery1->addMust($terms1);

        $queryId1 = new \Elastica\Query\Terms();
        $queryId1->setTerms('id', [$user->getId()]);

        $nestedQuery1 = new \Elastica\Query\Nested();
        $nestedQuery1->setPath('user');
        $nestedQuery1->setQuery($queryId1);
        $boolQuery1->addMust($nestedQuery1);


        $buckets = $finderBucket->search($boolQuery1);


        $boolQuery2 = new \Elastica\Query\BoolQuery();
        $termsTitle = new \Elastica\Query\Terms();
        $termsContent = new \Elastica\Query\Terms();
        $termsTitle->setTerms('title', array($searchKey));
        $boolQuery2->addMust($termsTitle);
        
        $termsContent->setTerms('content', array($searchKey));
        $boolQuery2->addShould($termsContent);

        $queryId2 = new \Elastica\Query\Terms();
        $queryId2->setTerms('id', [$user->getId()]);

        $nestedQuery2 = new \Elastica\Query\Nested();
        $nestedQuery2->setPath('user');
        $nestedQuery2->setQuery($queryId2);
        $boolQuery2->addMust($nestedQuery2);

        $items = $finderItem->search($boolQuery2);

        return [
            'searchKey' => $searchKey,
            'buckets' => $buckets,
            'items' => $items,
            'allBuckets' => $this->getAllBuckets($user)
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
