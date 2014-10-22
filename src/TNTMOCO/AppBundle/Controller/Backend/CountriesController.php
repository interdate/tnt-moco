<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Intl\Intl;

use TNTMOCO\AppBundle\Entity\Country;
use TNTMOCO\AppBundle\Entity\User;
use TNTMOCO\AppBundle\Form\Type\UserType;


class CountriesController extends Controller
{
    public function indexAction()
    {	
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');    	
    	$countries = $countryRepo->findAll();
    	return $this->render('TNTMOCOAppBundle:Backend/Countries:index.html.twig', array('countries' => $countries));    	
    }
    
    public function assignAction()
    {	
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country'); 
    	$userRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:User');
    	$countries = $countryRepo->findByIsActive(true);
    	$users = $userRepo->findAll();
    	
    	$user = new User();
    	$request = $this->getRequest();
    	$em = $this->getDoctrine()->getManager();    	
    	$userForm = $this->createForm(new UserType($em, $user), $user);
    	$adminForm = $this->createForm(new UserType($em, $user), $user);
    	//$userForm->handleRequest($request);
    	
    	return $this->render('TNTMOCOAppBundle:Backend/Countries:assign.html.twig', array(
    		'countries' => $countries,
    		'userForm' => $userForm->createView(),
    		'adminForm' => $adminForm->createView(),
    		'users' => $users,
    	));
    	
    	
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



