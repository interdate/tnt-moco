<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Intl\Intl;

use TNTMOCO\AppBundle\Entity\Country;

class CountriesController extends Controller
{
    public function indexAction()
    {	
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');    	
    	$countries = $countryRepo->findAll();    	
    	return $this->render('TNTMOCOAppBundle:Backend/Countries:index.html.twig', array('countries' => $countries));
    }
    
    public function editAction()
    {
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country'); 
    	$countries = $countryRepo->findByIsActive(true);
    	return $this->render('TNTMOCOAppBundle:Backend/Countries:edit.html.twig', array('countries' => $countries));
    }
    
    public function propertyAction($countryId, $setter, $value)
    {
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');
    	$country = $countryRepo->find($countryId);
    	$country->$setter($value);
    	
    	$em = $this->getDoctrine()->getManager();
    	$em->persist($country);
    	$em->flush();
    	die;
    	
    	//return $this->render('TNTMOCOAppBundle:Backend/Countries:index.html.twig', array('countries' => $countries));
    }
}


