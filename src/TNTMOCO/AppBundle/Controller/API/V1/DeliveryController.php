<?php

namespace TNTMOCO\AppBundle\Controller\API\V1;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\BrowserKit\Request;


class DeliveryController extends FOSRestController{
	
	/**	 
	 * 
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get delivery status list",
	 *   output = "TNTMOCO\AppBundle\Entity\DeliveryStatus",
	 *   statusCodes = {
	 *     200 = "Returned when successful",
	 *     401 = "Returned when bad credentials were sent"
	 *   }
	 * )
	 */
	public function getStatusesAction()
	{
		return array('deliveryStatusList' => $this->getDoctrine()->getRepository('TNTMOCOAppBundle:DeliveryStatus')->findAll());
	}

	
	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Get specific delivery status",
	 *   input = "TNTMOCO\AppBundle\Entity\DeliveryStatus",
	 *   statusCodes = {
	 *     200 = "Returned when successful",
	 *     401 = "Returned when bad credentials were sent",
	 *     404 = "Returned when resource was not found"
	 *   }
	 * )
	 */
	public function getStatusAction($id)
	{
		$deliveryStatus = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:DeliveryStatus')->find($id);
				
		if( !$deliveryStatus ){
			//throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
			$statusCode = 404;
			$responseBody = array(
				'status' => 'ERROR',
				'statusCode' => $statusCode,
				'message' => sprintf('The resource \'%s\' was not found.', $id),
			);
			
			$response = $this->view($responseBody, $statusCode);
			return $this->handleView($response);
			
		}
		
		return array(
			'status' => 'SUCCESS',
			'statusCode' => 200,
			'deliveryStatus' => $deliveryStatus,					
		);
	}
}