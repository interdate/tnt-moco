<?php

namespace TNTMOCO\AppBundle\Controller\API\V1;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use TNTMOCO\AppBundle\Factory\FileFactory;


class FilesController extends FOSRestController{
	
	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Upload Pick Up files",
	 *   
	 *   
     *   parameters={
     *      {"name"="operationId", "dataType"="string", "required"="true", "description"="An ID of the one of possible operations (PU, DL, PP correspond to Pick Up, Delivery and Postpone accordingly)"},
     *      {"name"="location", "dataType"="string", "required"="true", "description"="Current location coordinates"},
     *      {"name"="filesNumber", "dataType"="string", "required"="true", "description"="Total number of files that must be uploaded"},
     *   },
	 *   statusCodes = {
	 *     200 = "Returned when successful",
	 *     401 = "Returned when bad credentials were sent"
	 *   }
	 * )
	 */
	
	
	public function postFilesAction()
	{
		$request = $this->get('request');
		$files = $request->files;
		$em = $this->getDoctrine()->getManager();
		//$fileRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:File');
		$type = $request->request->get('operationId');
		$user = $this->get('security.context')->getToken()->getUser();
		
		foreach ($files as $uploadedFile) {
			if($uploadedFile instanceof UploadedFile){
				$file = FileFactory::create($type);
				$file->setFile($uploadedFile);				
				$file->setUser($user);
				$file->setLocation('test location');
				$file->setDatetime(new \DateTime());
				$em->persist($file);
				$em->flush();
				$file->preUpload();
				$file->upload();
			}
		}
		
		return array( 'response' => $request->request->all() );
			
	}
}