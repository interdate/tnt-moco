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
		//$location = $request->request->get('location');
		$location = 'dsa445ds456';
		$filesUrl = array();
		$i = 1;
		foreach ($files as $uploadedFile) {
			if($uploadedFile instanceof UploadedFile){
				
				//$request->request->set('file', $request->request->get('file_' . $i));
				
				
				$file = FileFactory::create($type, $user);
				$file->setFile($uploadedFile);
				$form = $this->createFormBuilder($file)->add('file')->getForm();				
				$form->handleRequest($request);
				
				print_r($form->getErrorsAsString());
				
				//print_r( get_class_methods($form));
				die;
				
				if ($form->isValid()) {
					
					$file->setLocation($location);
					$file->setDatetime(new \DateTime());
					$em->persist($file);
					$em->flush();
					$file->preUpload();
					$file->upload();
					$filesUrl[] = $request->getHost() . '/display/files/' .  $file->getId();
					echo "YES<br>";
				}				
				else{
					
					//var_dump($form->getChildren()['file']->getErrors());
					
					
					$errors = array();
					
					
						foreach ($form->childErrors() as $error) {
							$errors[] = $error->getMessage();
						}
					
						
					
					
					var_dump($errors);
					
					
					
					
					
					
					
					
					return array( 'response' => $form->getErrors() );
					
					echo count($form->getErrors() );
					foreach ($form->getErrors() as $error){
						echo get_class($error) . '<br>';
						echo $error;
					}
					die;
				}
				
				
				
				
			}
			
			$i++;
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