<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Intl\Intl;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use TNTMOCO\AppBundle\Entity\User;
use TNTMOCO\AppBundle\Entity\UserCountries;
use TNTMOCO\AppBundle\Entity\Country;

use TNTMOCO\AppBundle\Form\Type\UserType;
use TNTMOCO\AppBundle\Form\Type\EditUserType;
use Doctrine\Common\Collections\ArrayCollection;

class UsersController extends Controller
{
    public function indexAction()
    {	
    	$userRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:User');    	
    	$users = $userRepo->findAll();    	
    	return $this->render('TNTMOCOAppBundle:Backend/Users:index.html.twig', array('users' => $users));
    }
    
    public function createAction()
    {    	
    	$user = new User();
    	$em = $this->getDoctrine()->getManager();
    	return $this->handleUserAction($user, new UserType($em, $user));
    }
    
    public function editAction($userId)
    {    	
    	$userRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:User');
    	$em = $this->getDoctrine()->getManager();
    	$user = $userRepo->find($userId);

    	
    	
    	return $this->handleUserAction($user, new EditUserType($em, $user));
    }
    
    
    public function handleUserAction($user, $formType){
    	$userRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:User');
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');
    	$countries = $countryRepo->findByIsActive(true);
    	$userCountriesRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:UserCountries');
    	$depotRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Depot');
    	$em = $this->getDoctrine()->getManager();
    	$request = $this->getRequest();
    	$post = $request->request->all();    	
    	$choosenRole = isset($post['user']['role']) ? $post['user']['role'] : '';
    	$created = false;
    	$error = false;
    	
    	$params = explode('::',$request->attributes->get('_controller'));    	
    	$actionName = substr($params[1],0,-6);
    	 
    	if(isset($post['user']['username'])){
    	    		
    		$userForm = $this->createForm($formType, $user);
    		$userForm->handleRequest($request);
    		
    		
    		if($userForm->isValid()){
    			    			
    			if(count($user->getCountries()) == 0){
    				$user->addCountry($user->getCountry());
    			}
    			
    			
    			
    			
    			 
    			foreach ($user->getCountries() as $country){
    				
    				echo get_class($country) . '<br>';
    				
    				/*
    				$userCountry = new UserCountries();
    				$userCountry->setUser($user);
    				$userCountry->setCountry($country);
    				$user->removeCountry($country);
    				$user->addCountry($userCountry);
    				*/
    			}
    			
    			
    			
    			echo count($user->getCountries());
    			die;
    			
    			if($user->getRole()->getRole() == 'ROLE_COUNTRY_ADMIN' && !isset($post['confirm'])){
    				
    				$alreadyAssignedAdmins = $userRepo->getUsersAssignedToUserCountries($user);
    				
    				if(count($alreadyAssignedAdmins) > 0){    					
    					return $this->render('TNTMOCOAppBundle:Backend/Users:' . $actionName . '.html.twig', array(
    						'editedUser' => $user,
    						'userForm' => $userForm->createView(),
    						'countries' => $countries,
    						'choosenRole' => $choosenRole,
    						'created' => $created,
    						'alreadyAssignedAdmins' => $alreadyAssignedAdmins,
    						'userShouldConfirmAssigning' => true,
    						'error' => $error
    					));
    				}
    			}
    				
    			$depot = $depotRepo->find($post['user']['depot']);
    			$user->setDepot($depot);
    			
    			
    			 
    			 
    			if(isset($post['confirm'])){
    				$alreadyAssignedAdmins = $userRepo->getUsersAssignedToUserCountries($user);
    				 
    				foreach ($user->getCountries() as $userCountry ){
    					$userCountries[] = $userCountry->getCountry();
    				}
    				 
    				foreach ($alreadyAssignedAdmins as $admin){
    					foreach ($admin->getCountries() as $adminCountry){
    						if(in_array($adminCountry->getCountry(),  $userCountries)){
    							$em->remove($adminCountry);
    							$em->flush();
    						}
    					}
    				}
    			}
    			 
    			$factory = $this->get('security.encoder_factory');
    			$encoder = $factory->getEncoder($user);
    			$encodedPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());
    			$user->setPassword($encodedPassword);
    			$em->persist($user);
    			$em->flush();
    	
    			$created = true;
    			 
    			$userForm = $this->createForm($formType, new User());
    			$choosenRole = '';
    	
    		}
    		else{
    			$error = true;
    		}
    	
    		 
    	}
    	else{    		
    		$userForm = $this->createForm($formType, $user);
    		$userForm->handleRequest($request);
    	}
    	
    	 
    	return $this->render('TNTMOCOAppBundle:Backend/Users:' . $actionName . '.html.twig', array(
    		'editedUser' => $user,    		
    		'userForm' => $userForm->createView(),
    		'countries' => $countries,
    		'choosenRole' => $choosenRole,
    		'created' => $created,
    		'userShouldConfirmAssigning' => false,
    		'error' => $error
    	));
    	
    	
    }
    
}


