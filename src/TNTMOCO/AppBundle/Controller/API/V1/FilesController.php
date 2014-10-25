<?php

namespace TNTMOCO\AppBundle\Controller\API\V1;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use TNTMOCO\AppBundle\Factory\FileFactory;
use Symfony\Component\Form\Form;


class FilesController extends FOSRestController{
	
	/**
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Upload Pick Up files",
	 *   
	 *   
     *   parameters={
     *      {"name"="operationCode", "dataType"="string", "required"="true", "description"="A code of the one of possible operations (PU, DL, PP correspond to Pick Up, Delivery and Postpone accordingly)"},
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
		$type = $request->request->get('operationCode');
		$user = $this->get('security.context')->getToken()->getUser();
		//$location = $request->request->get('location');
		$location = 'dsa445ds456';
		$filesUrl = array();
		
		foreach ($files as $uploadedFile) {
			if($uploadedFile instanceof UploadedFile){
				$file = FileFactory::create($type, $user);
				$file->setFile($uploadedFile);				
				
				$validator = $this->get('validator');
				$errors = $validator->validate($file);
				
				if(count($errors) > 0){
					$fileName = $uploadedFile->getClientOriginalName();
					foreach ($errors as $error){						
						$errorsMessages[$fileName][] =  $error->getMessage();
					}					
									
				}
				
				print_r($errorsMessages);
				die;

				$file->setLocation($location);
				$file->setDatetime(new \DateTime());
				$em->persist($file);
				$em->flush();
				$file->preUpload();
				$file->upload();
				$filesUrl[] = $request->getHost() . '/display/files/' .  $file->getId();
				echo "YES<br>";				
			}			
		}
		
		if(count($errorsMessages) > 0){
			return array( 'response' => $errorsMessages );
		}
		
		$pdf = FileFactory::create('CN', $user);
		$pdf->setExt('pdf');
		$pdf->setDatetime(new \DateTime());
		$em->persist($pdf);
		$em->flush();
		
		$this->get('knp_snappy.pdf')->generate($filesUrl, $pdf->getAbsolutePath());		
		return array( 'response' => $request->request->all() );
			
	}
}