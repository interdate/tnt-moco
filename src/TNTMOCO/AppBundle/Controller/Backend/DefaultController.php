<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    public function indexAction()
    {
        /*
    	$user = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:User')->find(1);
    	$factory = $this->get('security.encoder_factory');
    	$encoder = $factory->getEncoder($user);
    	$salt = $user->getSalt();
    	$encodedPassword = $encoder->encodePassword('test', $salt);
    	
    	
    	$user->setPassword($encodedPassword);
    	$doctrine = $this->getDoctrine();
    	$em = $doctrine->getManager();
    	$em->persist($user);
    	$em->flush();
    	*/
    	/*
    	
    	$countries = Intl::getRegionBundle()->getCountryNames();
    	$em = $this->getDoctrine()->getManager();
    	
    	foreach ($countries as $key => $val) {    		
    		$country = new Country();
    		$country->setCode($key);
    		$country->setName($val);
    		$country->setActive(false);
    		$em->persist($country);
    		$em->flush();
    	}
    	die;
    	*/

    	
    	return $this->render('TNTMOCOAppBundle:Backend/Default:index.html.twig');
    }
}


