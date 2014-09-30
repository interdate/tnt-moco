<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\HttpFoundation\Request;


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
}
