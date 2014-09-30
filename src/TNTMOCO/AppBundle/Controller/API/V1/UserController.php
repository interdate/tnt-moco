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
		//return array('user' => $this->get('security.context')->getToken()->getUser());
				
		
		return array(
			'user' => array(
				'id' => '1',
				'username' => 'testusername',
				'email' => 'test@test.com',
				'countryId' => '153',
				'countryName' => 'Israel',
				'depotId' => '125487',
				'depotName' => 'Some Depot Name'	
			),
		);
		
		
	}	
}