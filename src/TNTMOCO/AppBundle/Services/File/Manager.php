<?php

namespace TNTMOCO\AppBundle\Services\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use TNTMOCO\AppBundle\Entity\ImageFile;
use TNTMOCO\AppBundle\Entity\TextFile;
use TNTMOCO\AppBundle\Entity\PdfFile;
use JMS\Serializer\Tests\Fixtures\Publisher;



class Manager 
{
	public $batchCode;
	
	public $fileType;
	
	public $filesUrl = array();
	
	private $errors = array();
	
	private $em;
	
	private $factory;
	
	private $user;
	
	private $uploadRootDir;
	
	public function __construct($em, $container){
		$this->em = $em;
		$this->factory = new Factory();	
		$this->user = $container->get('security.context')->getToken()->getUser();	
	}
	
	public function setBatchCode($batchCode){
		$this->batchCode = $batchCode;
	}
	
	public function getBatchCode(){
		return $this->batchCode;
	}
	
	public function setFileType($fileType){
		$this->fileType = $fileType;
	}
	
	public function getFileType(){
		return $this->fileType;
	}
	
	public function setFilesUrl($filesUrl){
		$this->filesUrl = $filesUrl;
	}
	
	public function getFilesUrl(){
		return $this->filesUrl;
	}
	
	public function getErrors(){
		return $this->errors;
	}
	
	public function getFactory(){
		return $this->factory;
	}
	
	public function getUser(){
		return $this->user;
	}
	
	public static function getFileRepo($fileType, $doctrine){
		switch ($fileType){
			case 'PU':
				return $doctrine->getRepository('TNTMOCOAppBundle:ImageFile');
				break;
		
			case 'DL':
			case 'PP':
				return $doctrine->getRepository('TNTMOCOAppBundle:TextFile');
				break;
		
			case 'CN':
				return $doctrine->getRepository('TNTMOCOAppBundle:PdfFile');
				break;
		
		}
	}
	
	public function getUploadRootDir(){		
		if(empty($this->uploadRootDir)){
			$file = $this->factory->create($this->fileType);
			$file->setUser($this->user);
			$file->setBatchCode($this->batchCode);
			$this->uploadRootDir = $file->getUploadRootDir();			
		}
		
		return $this->uploadRootDir;
	}
	
	public static function removeDir($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
	
	public function getUploadedFilesNumber($files){
		$uploadedFilesNumber = 0;
		foreach ($files as $uploadedFile) {
			if($uploadedFile instanceof UploadedFile){
				$uploadedFilesNumber++;
			}
		}
		
		return $uploadedFilesNumber;
	}	
	
	public function getWaitingFiles(){		
		return is_dir($this->getUploadRootDir()) ? array_diff(scandir($this->getUploadRootDir()), array('.','..')) : array();
	}
	
	public function uploadFiles($request, $validator){
		
		$waitingFiles = $this->getWaitingFiles();

		if(count($waitingFiles) > 0){
			foreach ($waitingFiles as $waitingFileName) {
				$waitingFileNameArr = explode(".", $waitingFileName);
				$fileId = $waitingFileNameArr[0];
				$this->filesUrl[] = $request->getHost() . '/files/display/' . $this->batchCode . '/' . $fileId;
			}
		}
		
		foreach ($request->files as $uploadedFile) {
			if($uploadedFile instanceof UploadedFile){
				$file = $this->factory->create($this->fileType);
				$file->setUser($this->user);
				$file->setFile($uploadedFile);
				$file->setBatchCode($this->batchCode);
				
				$errors = $validator->validate($file);
		
				if(count($errors) > 0){
					$fileName = $uploadedFile->getClientOriginalName();
					foreach ($errors as $error){
						$this->errors[$fileName][] =  $error->getMessage();
					}
				}
				else{
					$file->setLocation($request->request->get('location'));
					$file->setDatetime(new \DateTime());
					$this->em->persist($file);
					$this->em->flush();
					$file->preUpload();
					$file->upload();
					$this->filesUrl[] = $request->getHost() . '/files/display/' . $file->getBatchCode() . '/' . $file->getId();
				}
			}
		}
	}
}