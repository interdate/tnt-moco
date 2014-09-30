<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GlobalController extends Controller
{
    public function entityMethodAction($entityName, $entityId, $entityMethod, $value)
    {
    	$entityRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:' . $entityName);
    	$entity = $entityRepo->find($entityId);
    	$entity->$entityMethod($value);
    	 
    	$em = $this->getDoctrine()->getManager();
    	$em->persist($entity);
    	$em->flush();
    	die;
    }    
    
}


