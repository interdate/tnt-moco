<?php

namespace TNTMOCO\AppBundle\EventListener;

use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Doctrine\ORM\EntityManager;



class AuthenticationFailureListener implements EventSubscriberInterface
{
	protected $entityManager = null;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	public static function getSubscribedEvents()
	{
		return array(
				AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
				AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess'
		);
	}

	public function onAuthenticationFailure(AuthenticationFailureEvent $event)
	{
		$token = $event->getAuthenticationToken();
		$username = $token->getUsername();
		
		$userRepo = $this->entityManager->getRepository('TNTMOCOAppBundle:User');
		$user = $userRepo->findOneByUsername($username);
		$loggedAttempt = $user->getLoggedAttempt();
		if($loggedAttempt == 2){
			$user->setIsNonLocked(false);
		}else{
			$newLoggedAttempt = ($loggedAttempt) ? $loggedAttempt + 1 : 1;
			$user->setLoggedAttempt($newLoggedAttempt);
		}
		$this->entityManager->persist($user);
		$this->entityManager->flush();
		// ...
	}
	
	public function onAuthenticationSuccess(AuthenticationEvent $event)
	{
		$token = $event->getAuthenticationToken();
		$username = $token->getUsername();
		
		$userRepo = $this->entityManager->getRepository('TNTMOCOAppBundle:User');
		$user = $userRepo->findOneByUsername($username);
		$user->setLoggedAttempt(0);
		$this->entityManager->persist($user);
		$this->entityManager->flush();
		// ...
	}
	
}


