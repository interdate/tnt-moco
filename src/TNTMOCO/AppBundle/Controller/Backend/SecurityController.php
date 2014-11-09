<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use TNTMOCO\AppBundle\Form\Type\RecoveryType;
use \Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
//use TNTMOCO\AppBundle\Entity\User;


class SecurityController extends Controller{
	
    public function loginAction(Request $request)
    {    	
        $session = $request->getSession();         
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
        	$error = $request->attributes->get(
        			SecurityContext::AUTHENTICATION_ERROR
        	);
        } else {
        	$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        	$session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
         
        $result = array('last_username' => $session->get(SecurityContext::LAST_USERNAME), 'error' => $error);        
        return $this->render('TNTMOCOAppBundle:Backend/Security:index.html.twig', $result);              
    }
    
    public function passwordRecoveryAction($code = false)
    {
    	if($this->getUser()){
    		return $this->redirect($this->generateUrl('home'));
    	}
    	$success = false;
    	$error = false;
    	$userRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:User');
    	$request = $this->get('request');
    	if($code){
    		$codeArr = explode('_0_', $code);
    		$userFromCode = $userRepo->find($codeArr[1]);
    		$codeCheck = md5($userFromCode->getEmail() . $userFromCode->getId()) . '_0_' . $userFromCode->getId();
    		if($code === $codeCheck){
    			$user = $userFromCode;
    			$originalEncodedPassword = $user->getPassword();
    		}else{
    			$error = true;
    			$user = false;
    		}
    	}    	
    	$form = ($code and $user) ? $this->createForm(new RecoveryType($user), $user) : $this->createForm(new RecoveryType());
    	if($request->isMethod('POST')){
    		$form->handleRequest($request);
    		if($form->isValid()){
    			$success = true;
    			$hostName = $this->getRequest()->getHost();
    			if($code){
    				$body = 'Recovery password!\n\nYour password has been changed.\nUsername: ' . $user->getUsername() . '\nPassword: ' . $user->getPassword();
    				$userRepo->sendMail('recovery@' . $hostName, $user->getEmail(), 'Recovery password on ' . $hostName, $body, $this->get('mailer'));
    				$userRepo->setUserPassword($user, $this->get('security.encoder_factory'), $originalEncodedPassword, false);
    				$em = $this->getDoctrine()->getManager();
    				$em->persist($user);
    				$em->flush();
    			}else{
    				$formRequect = $request->get('recovery');
    				$email = $formRequect['email'];
	    			$user = $userRepo->findOneByEmail($email);
	    			if($user){
	    				$codeSent = md5($user->getEmail() . $user->getId()) . '_0_' . $user->getId();
	    				$body = 'Recovery password!\n\nFor change your password go to this link: http://' . $hostName . '/recovery/' . $codeSent;
	    				$userRepo->sendMail('recovery@' . $hostName, $user->getEmail(), 'Recovery password on ' . $hostName, $body, $this->get('mailer'));  				
	    			}else{
	    				$error = true;
	    			}
    			}
    		}
    	}
    	return $this->render('TNTMOCOAppBundle:Backend/Security:passwordRecovery.html.twig', array(
    		'form' => $form->createView(),
    		'success' => $success,
    		'error' => $error,
    		'code' => ($code) ? true : false,
    	));
    }
    
    public function passwordGenerationAction()
    {
    	$generator = new ComputerPasswordGenerator();
    	$generator->setOptions(ComputerPasswordGenerator::OPTION_NUMBERS | ComputerPasswordGenerator::OPTION_UPPER_CASE | ComputerPasswordGenerator::OPTION_LOWER_CASE);
    	$generator->setLength(8);    	
    	
    	while($password = $generator->generatePassword()){
    		//
    		if(preg_match('/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])|\S*(?=\S*[[:punct:]])(?=\S*[A-Z])(?=\S*[\d])|\S*(?=\S*[[:punct:]])(?=\S*[a-z])(?=\S*[\d])|\S*(?=\S*[[:punct:]])(?=\S*[a-z])(?=\S*[A-Z])\S*$/', $password, $matches)){    			
    			break;
    		}
    	}
    	//var_dump($password);die;
    	return $this->render('TNTMOCOAppBundle:Backend/Security:passwordGeneration.html.twig', array(
    			'password' => $password    			
    	));
    }
}
