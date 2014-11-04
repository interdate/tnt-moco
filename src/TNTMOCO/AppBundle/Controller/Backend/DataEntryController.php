<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use TNTMOCO\AppBundle\Entity\PdfFile;


class DataEntryController extends Controller
{
    public function indexAction()
    {    	
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');
    	//$userRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:User');
    	$countries = $countryRepo->findByIsActive(true);
    	
    	return $this->render('TNTMOCOAppBundle:Backend/DataEntry:index.html.twig', array(
    		'countries' => $countries,
    	));
    }
    
    public function countryAction($countryId)
    {
    	$request = $this->getRequest();
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');
    	$depotRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Depot');
    	$countries = $countryRepo->findByIsActive(true);
    	$country = $countryRepo->find($countryId);
    	$orderBy = $request->query->get('orderBy', 'name');
    	$depots = $depotRepo->getOrderedDepots($country, $orderBy, $this->getUser());
    	    	    	 
    	return $this->render('TNTMOCOAppBundle:Backend/DataEntry:index.html.twig', array(
    		'countries' => $countries,
    		'depots' => $depots,
    		'orderBy' => $orderBy,
    	));
    }
    
    public function depotAction($depotId, $fileId, $prevFileId, $reject, $complete)
    {
    	
    	/*
    	echo $depotId .  " " .  $fileId .  " " .  $prevFileId .  " " .  $reject .  " " .  $complete;
    	echo $this->getRequest()->request->get('comments');
    	die;
    	*/
    	
    	$request = $this->getRequest();
    	$orderBy = $request->request->get('orderBy');
    	$orderBy = ( empty($orderBy) ) ? $request->query->get('orderBy', 'name') : $request->request->get('orderBy');
    	$em = $this->getDoctrine()->getManager();    	
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');
    	$depotRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Depot');
    	$fileRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:PdfFile');
    	$reasonRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:RejectionReason');
    	$countries = $countryRepo->findByIsActive(true);    	
    	$depot = $depotRepo->find($depotId);
    	
    	$usersIds = array();
    	
    	$data = array(
    		'countries' => $countries,    		
    		'depot' => $depot,
    		'orderBy' => $orderBy,
    	);

    	if(!empty($prevFileId)){    	
	    	$prevFile = $fileRepo->find($prevFileId);
	    	$prevFile->setIsCompleted($complete);
	    	$prevFile->setIsRejected($reject);
	    	$prevFile->setIsLocked(false);
	    	$prevFile->setOpenedBy(null);
	    	if($request->isMethod('POST')){
	    		$reasonId = $request->request->get('reasonId', null);
	    		$comments = $request->request->get('comments', null);
	    		$reason = $reasonRepo->find($reasonId);	    		
	    		$prevFile->setRejectionReason($reason);
	    		$prevFile->setComments($comments);	    		
	    	}
	    	$em->persist($prevFile);
	    	$em->flush();
	    	
	    	if($complete == 1 && is_file($prevFile->getAbsolutePath())){
	    		unlink($prevFile->getAbsolutePath());
	    	}
    	}    	
    	
    	if(!empty($fileId)){
    		$file = $fileRepo->find($fileId);
    		$file->setIsLocked(true);
    		$file->setOpenedBy($this->getUser());
    		$em->persist($file);    		
    		$em->flush();    		
    		$data['file'] = $file;
    		$limit = 1;
    		$nextFileIndex = 0;
    	}
    	else{
    		
    		foreach($depot->getUsers() as $user){
    			$usersIds[] = $user->getId();
    		}
    		
    		$limit = 2;
    		$nextFileIndex = 1;
    		$file = null;    		 
    	}    	
    	
    	$files = $fileRepo->getFiles($this->getUser(), $usersIds, $file, $limit);
    	
    	if(empty($fileId) && !empty($files[0])){
    		$file = $files[0];    		 
    		$file->setIsLocked(true);
    		$file->setOpenedBy($this->getUser());
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($file);
    		$em->flush();    		 
    		$data['file'] = $file;
    	}
    	
    	$nextFile = (!empty($files[$nextFileIndex])) ? $files[$nextFileIndex] : new PdfFile();
    	$data['nextFile'] = $nextFile;
    	
    	$data['depots'] = $depotRepo->getOrderedDepots($depot->getCountry(), $orderBy, $this->getUser());
    
    	return $this->render('TNTMOCOAppBundle:Backend/DataEntry:index.html.twig', $data);
    }
}





