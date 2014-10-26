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
     *      {"name"="operationCode", "dataType"="string", "required"=true, "description"="A code of the one of possible operations (PU, DL, PP correspond to Pick Up, Delivery and Postpone accordingly)"},
     *      {"name"="location", "dataType"="string", "required"=true, "description"="Current location coordinates"},
     *      {"name"="totalFilesNumber", "dataType"="string", "required"=true, "description"="Total number of files that must be uploaded"},
     *      {"name"="batchCode", "dataType"="string", "required"=false, "description"="In case all the files were not uploaded (e.g. one or more files are corrupted or don't correspond to required size or format etc.) you'll recive 'batchCode' in response. The 'batchCode' must be sent with next request containing lost files to complete this operation."},
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
		$filesUrl = array();
		$em = $this->getDoctrine()->getManager();
		$fileType = $request->request->get('operationCode');		
		$user = $this->get('security.context')->getToken()->getUser();		
		$location = $request->request->get('location');
		$batchCode = $request->request->get('batchCode');
		$totalFilesNumber = $request->request->get('totalFilesNumber');
		$fileManager = $this->get('file_manager');
		$fileFactory = $fileManager->getFactory();
		$fileRepo = $fileManager->getFileRepo($fileType, $this->getDoctrine());
		$waitingFilesNumber = 0;
		$errorsMessages = array();
		
		if(!empty($batchCode)){	
			$file = $fileFactory->create($fileType);
			$file->setUser($user);			
			$file->setBatchCode($batchCode);
			/*
			$fls = scandir($file->getUploadRootDir());
			foreach ( $fls as $f){
				print_r($f) . '<br>';
			}
			*/
			
			if(is_dir($file->getUploadRootDir())){
				$waitingFiles = array_diff(scandir($file->getUploadRootDir()), array('.','..'));
				$waitingFilesNumber = count( $waitingFiles );
			}
			else{
				$waitingFilesNumber = 0;
			}
			
			//$waitingFilesNumber = is_dir($file->getUploadRootDir()) ? count( array_diff(scandir($file->getUploadRootDir()), array('.','..')) ) : 0;
		}
				
		if($waitingFilesNumber == 0){
			
			$uploadedFilesNumber = 0;
			foreach ($files as $uploadedFile) {
				if($uploadedFile instanceof UploadedFile){
					$uploadedFilesNumber++;
				}
			}
			if($totalFilesNumber != $uploadedFilesNumber){
				return array( 'response' => 'The number of uploaded files not corresponds to the total number of files that must be uploaded' );
			}
			
			$batchCode = md5(uniqid(null, true));
			
			foreach ($files as $uploadedFile) {
				if($uploadedFile instanceof UploadedFile){					
					$file = $fileFactory->create($fileType);
					$file->setUser($user);
					$file->setFile($uploadedFile);
					$file->setBatchCode($batchCode);
			
					$validator = $this->get('validator');
					$errors = $validator->validate($file);
			
					if(count($errors) > 0){
						$fileName = $uploadedFile->getClientOriginalName();
						foreach ($errors as $error){
							$errorsMessages[$fileName][] =  $error->getMessage();
						}
					}
					else{
						$file->setLocation($location);
						$file->setDatetime(new \DateTime());
						$em->persist($file);
						$em->flush();
						$file->preUpload();
						$file->upload();
						$filesUrl[] = $request->getHost() . '/files/display/' . $file->getBatchCode() . '/' . $file->getId();						
					}
				}
			}			
		}
		else{			
			
			
			foreach ($waitingFiles as $waitingFile) {
				$waitingFileNameArr = explode(".", $waitingFile);
				$fileId = $waitingFileNameArr[0];
				$filesUrl[] = $request->getHost() . '/files/display/' . $file->getBatchCode() . '/' . $fileId;
			}
			
			$uploadedFilesNumber = 0;
			foreach ($files as $uploadedFile) {
				if($uploadedFile instanceof UploadedFile){
					$uploadedFilesNumber++;
				}
			}
			
			//echo $uploadedFilesNumber . '<br>';
			
			$lostFilesNumber = $totalFilesNumber - $waitingFilesNumber;
			
			if($lostFilesNumber != $uploadedFilesNumber){
				return array( 'response' => 'The number of uploaded files not corresponds to number of corrupted files previously uploaded' );
			}
			
			foreach ($files as $uploadedFile) {
				if($uploadedFile instanceof UploadedFile){
					$file = $fileFactory->create($fileType);
					$file->setUser($user);
					$file->setFile($uploadedFile);
					$file->setBatchCode($batchCode);
						
					$validator = $this->get('validator');
					$errors = $validator->validate($file);
						
					if(count($errors) > 0){
						$fileName = $uploadedFile->getClientOriginalName();
						foreach ($errors as $error){
							$errorsMessages[$fileName][] =  $error->getMessage();
						}
					}
					else{
						$file->setLocation($location);
						$file->setDatetime(new \DateTime());
						$em->persist($file);
						$em->flush();
						$file->preUpload();
						$file->upload();
						$filesUrl[] = $request->getHost() . '/files/display/' . $file->getBatchCode() . '/' . $file->getId();
					}
				}
			}
			
		}
		
		if(count($errorsMessages) > 0){
			return array( 'response' => $errorsMessages );
		}
		
		$pdf = $fileFactory->create('CN');
		$pdf->setExt('pdf');
		$pdf->setUser($user);
		$pdf->setDatetime(new \DateTime());
		$em->persist($pdf);
		$em->flush();		
		$this->get('knp_snappy.pdf')->generate($filesUrl, $pdf->getAbsolutePath());
		$fileManager->removeDir($file->getUploadRootDir());				
				
		return array( 'response' => $request->request->all() );
			
	}
}