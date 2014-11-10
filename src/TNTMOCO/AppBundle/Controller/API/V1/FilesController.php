<?php

namespace TNTMOCO\AppBundle\Controller\API\V1;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\BrowserKit\Request;

class FilesController extends FOSRestController{
	
	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Upload Pick Up files",
	 *   
	 *   
     *   parameters={
     *      {"name"="operationCode", "dataType"="string", "required"=true, "description"="A code of the one of possible operations (PU, DL, PP correspond to Pick Up, Delivery and Postpone accordingly)"},
     *      {"name"="location", "dataType"="string", "required"=true, "description"="Current location coordinates"},
     *      {"name"="totalFilesNumber", "dataType"="string", "required"=true, "description"="Total number of files that must be uploaded"},
     *      {"name"="batchCode", "dataType"="string", "required"=false, "description"="In case all the files were not uploaded (e.g. one or more files are corrupted or don't correspond to required size or format etc.) you'll recive 'batchCode' in response. The 'batchCode' must be sent with next request containing lost files to complete this operation."},
     *   },
	 *   statusCodes = {
	 *     201 = "Returned when successful. The PDF file has been created.",
	 *     400 = "
	      		Bad Request. 
	 			Possible reasons:
		 			1. The number of uploaded files not corresponds to the total number of files that must be uploaded (parameter 'totalFilesNumber');	  			
		 			2. One or more files are corrupted or don't correspond to required size or format; 
		 			3. The number of uploaded files not corresponds to number of corrupted files previously uploaded; 
		 			4. Another unknown reason.  	 		
	 		",
	 *     401 = "Returned when bad credentials were sent."
	 *   }
	 * )
	 */
	
	
	
	
	public function postFilesAction()
	{
		$request = $this->get('request');		
		$totalFilesNumber = $request->request->get('totalFilesNumber');
		$fileManager = $this->get('file_manager');
		$fileManager->setBatchCode($request->request->get('batchCode'));
		$fileManager->setFileType($request->request->get('operationCode'));		
		$fileRepo = $fileManager->getFileRepo($fileManager->getFileType(), $this->getDoctrine());
		$validator = $this->get('validator');
		$waitingFilesNumber = 0;
				
		$batchCode = $fileManager->getBatchCode();
		
		if(!empty($batchCode)){				
			$waitingFiles = $fileManager->getWaitingFiles();
			$waitingFilesNumber = count( $waitingFiles );			
		}

		$uploadedFilesNumber = $fileManager->getUploadedFilesNumber($request->files);
		
		if($waitingFilesNumber == 0){
			if($totalFilesNumber != $uploadedFilesNumber){
				$statusCode = 400;
				$responseBody = array('status' => 'ERROR', 'statusCode' => $statusCode, 'message' => 'The number of uploaded files not corresponds to the total number of files that must be uploaded');
				$response = $this->view($responseBody, $statusCode);
				return $this->handleView($response);				
			}				
			$fileManager->setBatchCode(md5(uniqid(null, true)));								
		}
		else{
			$lostFilesNumber = $totalFilesNumber - $waitingFilesNumber;			
			if($lostFilesNumber != $uploadedFilesNumber){
				$statusCode = 400;
				$responseBody = array('status' => 'ERROR', 'statusCode' => $statusCode, 'message' => 'The number of uploaded files not corresponds to number of corrupted files previously uploaded');
				$response = $this->view($responseBody, $statusCode);
				return $this->handleView($response);
			}
		}
		
		$fileManager->uploadFiles($request, $validator);		
		$errors = $fileManager->getErrors();
		
		if(count($errors) > 0){	
			$statusCode = 400;
			$responseBody = array('status' => 'ERROR', 'statusCode' => $statusCode, 'messages' => $errors);			
			if(count($fileManager->getWaitingFiles()) > 0){
				$responseBody['batchCode'] = $fileManager->getBatchCode();
			}			
			$response = $this->view($responseBody, $statusCode);
			return $this->handleView($response);
		}
				
		$pdf = $fileManager->getFactory()->create('CN');
		$pdf->setExt('pdf');
		$pdf->setUser($fileManager->getUser());
		$pdf->setDatetime(new \DateTime());
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($pdf);
		$em->flush();		
		
		$this->get('knp_snappy.pdf')->generate($fileManager->getFilesUrl(), $pdf->getAbsolutePath());
		$fileManager->removeDir($fileManager->getUploadRootDir());
						
		$statusCode = 201;
		$responseBody = array('status' => 'SUCCESS', 'statusCode' => $statusCode, 'message' => 'The PDF file has been created');
		$response = $this->view($responseBody, $statusCode);
		return $this->handleView($response);					
	}
}