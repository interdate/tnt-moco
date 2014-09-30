<?php

namespace TNTMOCO\AppBundle\Controller\API\V1;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\BrowserKit\Request;


class PostponeController extends FOSRestController{
	
	/**	 
	 * 
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get postpone reasons list",
	 *   output = "TNTMOCO\AppBundle\Entity\PostponeReason",
	 *   statusCodes = {
	 *     200 = "Returned when successful",
	 *     401 = "Returned when bad credentials were sent"
	 *   }
	 * )
	 */
	public function getReasonsAction()
	{
		return array('postponeReasonList' => $this->getDoctrine()->getRepository('TNTMOCOAppBundle:PostponeReason')->findAll());
	}

	
	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get specific postpone reason",
	 *   input = "TNTMOCO\AppBundle\Entity\PostponeReason",
	 *   statusCodes = {
	 *     200 = "Returned when successful",
	 *     401 = "Returned when bad credentials were sent"
	 *   }
	 * )
	 */
	public function getReasonAction($id)
	{
		$postponeReason = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:PostponeReason')->find($id);
		
		if( !$postponeReason ){
			throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
		}
		
		return array('postponeReason' => $postponeReason);
		
	}
}

