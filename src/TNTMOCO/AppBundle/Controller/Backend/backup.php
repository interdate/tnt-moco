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
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');
    	$depotRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Depot');
    	$countries = $countryRepo->findByIsActive(true);
    	$country = $countryRepo->find($countryId);
    	$depots = $depotRepo->findByCountry($country);
    	 
    	return $this->render('TNTMOCOAppBundle:Backend/DataEntry:index.html.twig', array(
    		'countries' => $countries,
    		'depots' => $depots,
    	));
    }
    
    public function depotAction($depotId, $fileId, $prevFileId, $reject, $complete)
    {
    	//echo $depotId .  " " .  $fileId .  " " .  $prevFileId .  " " .  $reject .  " " .  $complete;
    	//die;
    	
    	$em = $this->getDoctrine()->getManager();
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');
    	$depotRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Depot');
    	$fileRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:PdfFile');
    	$countries = $countryRepo->findByIsActive(true);    	
    	$depot = $depotRepo->find($depotId);    	
    	$depots = $depotRepo->findByCountry($depot->getCountry());
    	$usersIds = array();
    	
    	$data = array(
    		'countries' => $countries,
    		'depots' => $depots,
    		'depot' => $depot,    		
    	);
    	
    	foreach($depot->getUsers() as $user){
    		$usersIds[] = $user->getId();
    	}

    	if(!empty($prevFileId)){    	
	    	$prevFile = $fileRepo->find($prevFileId);
	    	$prevFile->setIsCompleted($complete);
	    	$prevFile->setIsRejected($reject);
	    	$prevFile->setIsLocked(false);
	    	$prevFile->setOpenedBy(null);
	    	$em->persist($prevFile);
	    	$em->flush();
    	}
    	else{
    		$prevFile = null;    		
    	}
    	
    	if(!empty($fileId)){
    		$file = $fileRepo->find($fileId);
    		$file->setIsLocked(true);
    		$file->setOpenedBy($this->getUser());
    		$em->persist($file);    		
    		$em->flush();    		
    		$data['file'] = $file;
    		$limit = 1;
    		$index = 0;
    	}
    	else{
    		$limit = 2;
    		$index = 1; 
    	}    	
    	
    	$files = $fileRepo->getFiles($this->getUser(), $usersIds, $prevFile, $limit);
    	
    	if(empty($fileId) && !empty($files[0])){
    		$file = $files[0];    		 
    		$file->setIsLocked(true);
    		$file->setOpenedBy($this->getUser());
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($file);
    		$em->flush();    		 
    		$data['file'] = $file;
    	}
    	
    	$nextFile = (!empty($files[$index])) ? $files[$index] : new PdfFile();
    	$data['nextFile'] = $nextFile;
    
    	return $this->render('TNTMOCOAppBundle:Backend/DataEntry:index.html.twig', $data);
    }
}


