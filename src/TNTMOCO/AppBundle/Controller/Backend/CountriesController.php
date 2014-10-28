<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Intl\Intl;

use TNTMOCO\AppBundle\Entity\Country;
use TNTMOCO\AppBundle\Entity\User;
use TNTMOCO\AppBundle\Form\Type\UserType;
use TNTMOCO\AppBundle\Entity\UserCountries;



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
    	$userCountriesRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:UserCountries');
    	
    	$countries = $countryRepo->findByIsActive(true);
    	
    	
    	$user = new User();
    	$request = $this->getRequest();
    	$em = $this->getDoctrine()->getManager();    	
    	$userForm = $this->createForm(new UserType($em, $user), $user);
    	$userForm = $userCountriesRepo->setUserFormField($userForm);
    	
    	$adminForm = $this->createForm(new UserType($em, $user), $user);
    	$adminForm = $userCountriesRepo->setUserFormField($adminForm, true);
    	
    	$request = $this->getRequest();    	
    	
    	$variable = false;
    	if($request->get('user')['role'] == 2){    		
    		$variable = $userCountriesRepo->saveUser($adminForm, $request, $user, $this->get('security.encoder_factory'));
    		$variable['userForm'] = $userForm->createView();
    		if($variable['created'] == true){
    			$user = new User();
    			$adminForm = $this->createForm(new UserType($em, $user), $user);
    			$adminForm = $userCountriesRepo->setUserFormField($adminForm, true);
    			$variable['adminForm'] = $adminForm->createView();
    		}
    	}elseif($request->get('user') != null)
    	{
    		$variable = $userCountriesRepo->saveUser($userForm, $request, $user, $this->get('security.encoder_factory'));
    		$variable['adminForm'] = $adminForm->createView();
    		if($variable['created'] == true){
    			$user = new User();
    			$userForm = $this->createForm(new UserType($em, $user), $user);
    			$userForm = $userCountriesRepo->setUserFormField($userForm);
    			$variable['userForm'] = $userForm->createView();
    		}
    	}
    	$users = $userRepo->findByIsNonLocked(true);

    	if(!$variable){
    		$variable = array(
	    		'userForm' => $userForm->createView(),
	    		'adminForm' => $adminForm->createView(),
    			'created' => ($variable['created']) ? true : false,
    			'userShouldConfirmAssigning' => false,
	    	);    		
    	}
    	$variable['countries'] = $countries;
    	$variable['users'] = $users;

    	return $this->render('TNTMOCOAppBundle:Backend/Countries:assign.html.twig', $variable);
    	
    	
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
    
    public function usersAssignAction()
    {
    	$request = $this->getRequest();
    	$usersId = $request->get('users');
    	$countryId = $request->get('country');
    	$confirm = $request->get('confirm');
    	
    	$userRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:User');
    	$userCountriesRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:UserCountries');
    	$country = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country')->find($countryId);
    	$em = $this->getDoctrine()->getManager();
    	$alreadyAssignedAdmins = array('admins' => false, 'users' => false);
    	
    	foreach ($usersId as $userId){
    		$user = $userRepo->find($userId);
    		$userRoleSystemName = $user->getRole()->getRole();
    		
    		if($userRoleSystemName == 'ROLE_COUNTRY_ADMIN'){    			
    			$user->addCountry($country);
    			$userRepo->createUserCountriesCollection($user);    				
    		
    			if($confirm){
    				$userRepo->unassignUsersAssignedToUserCountries($user);
    			}
    			else{
    				//var_dump($user->getCountries()[0] instanceof UserCountries);die;
    				$alreadyAssignedAdmins['admins'] = $userRepo->getUsersAssignedToUserCountries($user);
    				if(count($alreadyAssignedAdmins['admins']) > 0){
    					return $this->render('TNTMOCOAppBundle:Backend/Countries:usersAssign.html.twig', array(    								
    						'alreadyAssignedAdmins' => $alreadyAssignedAdmins,
    						'actionName' => '',
    						'created' => false 								
    					));    						    						
    				}
    			}
    		}
    		else{
    			if($user->getCountry() instanceof Country and !$confirm){
    				$alreadyAssignedAdmins['users'][] = $user;    				
    			}else{
    				$user->setCountry($country);
    			}    			
    		}    		
    		$userCountriesRepo->removeUserCountries($user);
    		$em->persist($user);
    		$em->flush();   
    	}
    	
    	return $this->render('TNTMOCOAppBundle:Backend/Countries:usersAssign.html.twig', array(
    		'alreadyAssignedAdmins' => $alreadyAssignedAdmins,
    		'actionName' => '',
    		'created' => true
    	));
    }
}



