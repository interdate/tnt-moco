<?php

namespace TNTMOCO\AppBundle\Controller\API\V1;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\BrowserKit\Request;

class UserController extends FOSRestController{
	
	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get logged user data",
	 *   
	 *   output="TNTMOCO\AppBundle\Entity\User",	 
	 *   statusCodes = {
	 *     200 = "Returned when successful",
	 *     401 = "Returned when bad credentials were sent"
	 *   }
	 * )
	 */
	
	//input = "TNTMOCO\AppBundle\Entity\User",
	
	
	public function getUserAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
		
		return array(
			'status' => 'SUCCESS',
			'statusCode' => 200,
			'user' => array(
				'id' => $user->getId(),
				'username' => $user->getUsername(),
				'email' => $user->getEmail(),
				'country' => array(
					'code' => $user->getCountry()->getCode(),
					'name' => $user->getCountry()->getName(),
				),			
				'depot' => array(
					'code' => '',
					'name' => $user->getDepot()->getName(),
				),
			)
		);

		
	}	
}