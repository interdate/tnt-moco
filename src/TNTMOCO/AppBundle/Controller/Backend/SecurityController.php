<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use TNTMOCO\AppBundle\Form\Type\RecoveryType;
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
    
    public function recoveryAction($code = false){
    	//var_dump(mail("pavel@interdate-ltd.co.il","Success","Great, Localhost Mail works"));die;
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
    			if($code){
    				$userRepo->setUserPassword($user, $this->get('security.encoder_factory'), $originalEncodedPassword);
    				$em = $this->getDoctrine()->getManager();
    				$em->persist($user);
    				$em->flush();
    			}else{
    				$formRequect = $request->get('recovery');
    				$email = $formRequect['email'];
	    			$user = $userRepo->findOneByEmail($email);
	    			$hostName = $this->getRequest()->getHost();
	    			if($user){
	    				$codeSent = md5($user->getEmail() . $user->getId()) . '_0_' . $user->getId();
	    				$body = 'Recovery password!\n\nFor change your password go to this link: http://' . $hostName . '/recovery/' . $codeSent;
	    				$message = \Swift_Message::newInstance()
		    				->setSubject('Recovery password on ' . $hostName)
		    				->setFrom('recovery@' . $hostName)
		    				->setTo($user->getEmail())
		    				->setBody($body);
	    				$this->get('mailer')->send($message);
	    			}else{
	    				$error = true;
	    			}
    			}
    		}
    	}
    	return $this->render('TNTMOCOAppBundle:Backend/Security:recovery.html.twig', array(
    		'form' => $form->createView(),
    		'success' => $success,
    		'error' => $error,
    		'code' => ($code) ? true : false,
    	));
    }
    /*
     public function passwordRecoveryAction(){
     $success = false;
     $request = $this->get('request');
    
     $form = $this->createFormBuilder()
     ->add('email', 'text', array(
     'label' => 'Email',
     'constraints' => array(
     new Constraints\NotBlank(),
     new Constraints\Email(array(
     'message' => 'The email "{{ value }}" is not a valid email.',
     'checkMX' => true
     ))
     )
     ))
     ->getForm();
    
     if($request->isMethod('POST')){
     $form->submit($request);
     if($form->isValid()){
     $success = true;
     $formRequect = $request->get('form');
     $email = $formRequect['email'];
     $doctrine = $this->getDoctrine();
     $user = $doctrine->getRepository('D4DAppBundle:Users')->findOneByUseremail($email);
     if($user){
     $em = $doctrine->getManager();
     $factory = $this->get('security.encoder_factory');
     $encoder = $factory->getEncoder($user);
     $pass = substr(sha1(uniqid(mt_rand(), true)), 0 , 7);
     $password = $encoder->encodePassword($pass, $user->getSalt());
     $user->setUserpass($password);
     	
     $em->persist($user);
     $em->flush();
     $this->sendMailAction($user, $pass, array('password'));
     }else{
     $success = 'error';
     }
     }
     }
     return $this->render('D4DAppBundle:Frontend/User:recoveryPassword.twig.html', array(
     'form' => $form->createView(),
     'success' => $success
     ));
     }
    
     public function sendMailAction($user, $pass = '', $templates = array()){
     	if(is_string($templates)) $templates = array($templates);
     	$templatesRepo = $this->getDoctrine()->getRepository('D4DAppBundle:LangDyncpages');
    
     	$code = md5($user->getUseremail() . $user->getUsernic() . $user->getUserId());
    
     	$hostName = $this->getRequest()->getHost();
    
     	$constantsValues = $templatesRepo->getConstantsValues(array('pass' => $pass, 'code' => $code, 'hostName' => $hostName, 'user' => $user));
    
     	if(count($templates)>0){
     		foreach($templates as $name){
     			$template = $templatesRepo->findOneByPagename($name);
     			$body = str_replace($constantsValues['find'], $constantsValues['values'], $template->getPagebody());
    
     			$message = \Swift_Message::newInstance()
     				->setSubject($template->getPagetitle() . ' ' . $hostName)
     				->setFrom($template->getPagename() . '@' . $hostName)
     				->setTo($user->getUseremail())
     				->setBody($body);
     			$this->get('mailer')->send($message);
     		}
     	}
     }
     */
}
