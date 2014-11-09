<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Intl\Intl;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Form\FormError;

use TNTMOCO\AppBundle\Entity\User;
use TNTMOCO\AppBundle\Entity\UserCountries;
use TNTMOCO\AppBundle\Entity\Country;

use TNTMOCO\AppBundle\Form\Type\UserType;
use TNTMOCO\AppBundle\Form\Type\EditUserType;
use TNTMOCO\AppBundle\Form\Type\CurrentUserType;
use TNTMOCO\AppBundle\Form\Type\UserPasswordType;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Tests\Fixtures\Publisher;

class UsersController extends Controller
{
    public function indexAction()
    {	
    	$userRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:User');
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');
    	$roleRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Role');
    	$currentUser = $this->getUser();
    	//$users = $userRepo->findAll();
    	
    	//$countries = $countryRepo->findByIsActive(true);
    	$countries = $countryRepo->findByUser($currentUser);
    	$roles = $roleRepo->createQueryBuilder('r')
		    ->where('r.id > ' . $currentUser->getRole()->getId())
		    ->orderBy('r.id', 'ASC')
		    ->getQuery()
    		->getResult();
		
    	$request = $this->getRequest();
    	if(count($countries) == 1){
    		$request->request->set('country', $countries[0]->getId());
    	}
		$depots = $userRepo->getSearchQuery($request, $currentUser, true);
		$query = $userRepo->getSearchQuery($request, $currentUser);
    	
    	$paginator  = $this->get('knp_paginator');
    	$users = $paginator->paginate(
    		$query,
    		$this->get('request')->query->get('page', 1)/*page number*/,
    		16/*limit per page*/
    	);
    	
    	return $this->render('TNTMOCOAppBundle:Backend/Users:index.html.twig', array(
    		'users' => $users,
    		'countries' => $countries,
    		'roles' => $roles,
    		'depots' => $depots,
    	));
    }
    
    public function createAction()
    {    	
    	$user = new User();
    	$em = $this->getDoctrine()->getManager();
    	
    	return $this->handleUserAction($user, new UserType($em, $user, $this->getUser()));
    }
    
    public function editAction($userId)
    {    	
    	$userRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:User');
    	$em = $this->getDoctrine()->getManager();
    	$user = $userRepo->find($userId);    	
    	
    	return $this->handleUserAction($user, new EditUserType($em, $user, $this->getUser()));
    }
    
    
    public function handleUserAction($user, $formType){
    	
    	$profile = ($user->getId() == $this->getUser()->getId()) ? true : false;
    	$userRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:User');
    	$roleRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Role');
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');
    	$countries = $countryRepo->findByIsActive(true);
    	$userCountriesRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:UserCountries');
    	$depotRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Depot');
    	$em = $this->getDoctrine()->getManager();
    	$request = $this->getRequest();
    	$post = $request->request->all();
    	$password = null;
    	$originalEncodedPassword = $user->getPassword();
    	
    	if(isset($post['user']['role'])){
    		$choosenRole = $post['user']['role'];
    	}
    	elseif(isset($post['role'])){
    		$choosenRole = $post['role'];
    	}
    	else{
    		$choosenRole = '';
    	}    		
    
    	$created = false;
    	$error = false;
    	
    	$params = explode('::',$request->attributes->get('_controller'));    	
    	$actionName = substr($params[1],0,-6);
    	
    	$userForm = $this->createForm($formType, $user);    	
    	$userForm->handleRequest($request);
    	    	 
    	if(isset($post['user']['username'])){
    		    		   		
    		if($userForm->isValid()){
    			
    			$checkOldPassword = ($profile) ? $userRepo->checkUserOldPassword($user, $this->get('security.encoder_factory'), $originalEncodedPassword) : true;
    			
    			$isExistUsername = $userRepo->findOneByUsername($user->getUsername());
    			$isExistEmail = $userRepo->findOneByEmail($user->getEmail());
    			
    			$userRepo->setUserPassword($user, $this->get('security.encoder_factory'), $originalEncodedPassword);
    			$userRoleSystemName = $user->getRoleSystemName();
    			
    			switch ($userRoleSystemName){
    				case 'ROLE_COUNTRY_ADMIN':    					
    					$user->setCountry(null);
    					$user->setDepot(null);
    					$userRepo->createUserCountriesCollection($user);
    					
    					if(isset($post['confirm'])){
    						$userRepo->unassignUsersAssignedToUserCountries($user);
    					}
    					else{
    						$alreadyAssignedAdmins = $userRepo->getUsersAssignedToUserCountries($user);    						    						
    						if(count($alreadyAssignedAdmins) > 0){    								
    							return $this->render('TNTMOCOAppBundle:Backend/Users:profile.html.twig', array(
    								'actionName' => $actionName,
    								'editedUser' => $user,
    								'userForm' => $userForm->createView(),
    								//'userPasswordForm' => $userPasswordFormView,
    								'countries' => $countries,
    								'choosenRole' => $choosenRole,
    								'created' => $created,
    								'alreadyAssignedAdmins' => $alreadyAssignedAdmins,
    								'userShouldConfirmAssigning' => true,
    								'error' => $error
    							));
    						}
    					}    					    					
    					break;
    				
    				case 'ROLE_COURIER':
    					$depot = $depotRepo->find($post['user']['depot']);
    					$user->setDepot($depot);
    					break;
    					
    				case 'ROLE_USER':
    				case 'ROLE_CUSTOMER_SERVICE':
    					$user->setDepot(null);
    					break;
    			}
				
    			if(($isExistUsername and $isExistUsername->getId() !== $user->getId()) or ($isExistEmail and $isExistEmail->getId() !== $user->getId()) or !$checkOldPassword){
    				if(!$checkOldPassword){
    					$userForm->get('oldPassword')->addError(new FormError('Old Password is not correct'));
    					$error = true;
    				}
    				if($isExistUsername and $isExistUsername->getId() !== $user->getId()){
    					$userForm->get('username')->addError(new FormError('Username is already exists'));
    					$error = true;
    				}
    				if($isExistEmail and $isExistEmail->getId() !== $user->getId()){
    					$userForm->get('email')->addError(new FormError('Email is already exists'));
    					$error = true;
    				}
    			}else{
	    			$userCountriesRepo->removeUserCountries($user);    			
	    			$em->persist($user);
	    			$em->flush();
	    			
	    			$user = ($actionName == 'edit' or $profile) ? $user : new User();
	    			   			 
	    			$userForm = $this->createForm($formType, $user);    			
	    			$choosenRole = '';
	    			$created = true;	    			
    			}
    	
    		}
    		else{
    			$error = true;    			
    		}    	
    		 
    	}
    	else{    		
    		if(!empty($choosenRole)){
    			$role = $roleRepo->find($choosenRole);	
    			$user->setRole($role);
    		}    		
    	}  	
    	
    	return $this->render('TNTMOCOAppBundle:Backend/Users:profile.html.twig', array(
    		'actionName' => $actionName,
    		'editedUser' => $user,    		
    		'userForm' => $userForm->createView(),
    		//'userPasswordForm' => $userPasswordFormView,
    		'countries' => $countries,
    		'choosenRole' => $choosenRole,
    		'created' => $created,
    		'userShouldConfirmAssigning' => false,
    		'error' => $error
    	));
    	
    	
    }
    
    public function depotsAction(){
    	$request = $this->getRequest();
    	$countryId = $request->query->get('countryId');
    	$countryId = ($countryId == null) ? $request->get('countryId') : $countryId; 
    	
    	$country = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country')->find($countryId);
    	$depots = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Depot')->findByCountry($country);
    	
    	$list['name'] = 'depot';
    	$list['id'] = 'search_depot';
    	$list['empty_value'] = 'Depot';
    	$list['items'] = $depots;
    	
    	return $this->render('TNTMOCOAppBundle:Backend/Common:select.html.twig', array(
    		'list' => $list,
    	));
    }
    
    public function editProfileAction(){
    	$user = $this->getUser();
    	$em = $this->getDoctrine()->getManager();     	
    	 
    	return $this->handleUserAction($user, new CurrentUserType($em, $user));
    }
    
    
}


/*
 foreach ($user->getCountries() as $country){
echo get_class($country) . '<br />';
echo $country . '<br /><br />';
}
die;
*/


/*
 if($user->getCountry()){
$user->addCountry($user->getCountry());
}
*/
