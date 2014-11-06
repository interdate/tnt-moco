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
    	$countries = $countryRepo->findByIsActive(true);
    	$data = array('countries' => $countries);
    	$data = $this->tryDefineRoles($data);
    	return $this->render('TNTMOCOAppBundle:Backend/DataEntry:index.html.twig', $data);
    }
    
    public function countryAction($countryId, $roleId)
    {
    	$request = $this->getRequest();
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');
    	$depotRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Depot');
    	$countries = $countryRepo->findByIsActive(true);
    	$country = $countryRepo->find($countryId);
    	$orderBy = $request->query->get('orderBy', 'name');
    	$depots = $depotRepo->getOrderedDepots($country, $orderBy, $this->getUser(), $roleId);    	
    	
    	$data = array(
    		'countries' => $countries,
    		'depots' => $depots,
    		'orderBy' => $orderBy,
    	);    	
    	
    	$data = $this->tryDefineRoles($data);
    	    	    	 
    	return $this->render('TNTMOCOAppBundle:Backend/DataEntry:index.html.twig', $data);
    }
    
    public function depotAction($depotId)
    {
    	$request = $this->getRequest();    	
    	$fileId = $request->request->get('fileId', 0);
    	$prevFileId = $request->request->get('prevFileId', 0);
    	$complete = $request->request->get('complete', false);
    	$reject = $request->request->get('reject', false);
    	$delete = $request->request->get('delete', false);
    	$orderBy = $request->request->get('orderBy');
    	$roleId =  $request->request->get('roleId', 0);
    
    	$orderBy = ( empty($orderBy) ) ? $request->query->get('orderBy', 'name') : $orderBy;
    	 
    	$currentUser = $this->getUser();
    	$em = $this->getDoctrine()->getManager();
    	$roleRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Role');
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');
    	$depotRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Depot');
    	$fileRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:PdfFile');
    	$reasonRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:RejectionReason');
    	$logRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Log');    	
    	$countries = $countryRepo->findByIsActive(true);
    	$depot = $depotRepo->find($depotId);
    	 
    	$usersIds = array();
    	
    	foreach($depot->getUsers() as $user){
    		$usersIds[] = $user->getId();
    	}
    	 
    	$data = array(
    		'countries' => $countries,
    		'depot' => $depot,
    		'orderBy' => $orderBy,
    	);
    	
    	$files = $fileRepo->getFiles($currentUser, $usersIds, $prevFileId, 1, $roleId);
    
    	if(!empty($prevFileId)){
    		$prevFile = $fileRepo->find($prevFileId);
    		$prevFile->setIsCompleted($complete);
    		$prevFile->setIsRejected($reject);
    		$prevFile->setIsDeleted($delete);
    		$prevFile->setIsLocked(false);
    		$prevFile->setOpenedBy(null);
    		if($reject){
    			$reasonId = $request->request->get('reasonId', null);
    			$comments = $request->request->get('comments', null);
    			if(null !== $reasonId){
	    			$reason = $reasonRepo->find($reasonId);
	    			$prevFile->setRejectionReason($reason);
    			}    			
    			$prevFile->setComments($comments);
    			
    			$logRepo->saveLog($prevFile->getId(), 'CN', 'rejected', $prevFile->getUser()->getId());
    		}
    		else{
    			$completedInfo = $request->request->get('completedInfo', null);
    			if(null !== $completedInfo){
    				$prevFile->setCompletedInfo($completedInfo);
    			}
    		}
    		
    		$em->persist($prevFile);
    		$em->flush();
    
    		if( ($complete || $delete) && is_file($prevFile->getAbsolutePath())){
    			unlink($prevFile->getAbsolutePath());
    			if($delete){
    				$logRepo->saveLog($prevFile->getId(), 'CN', 'deleted', $prevFile->getUser()->getId());
    			}
    		}
    	}    	
    	
    	if($prevFileId > 0 && empty($files[0])){
    		$prevFileId = 0;
    		$files = $fileRepo->getFiles($currentUser, $usersIds, $prevFileId, 1, $roleId);
    	}
    	 
    	if(!empty($files[0])){
    		$file = $files[0];
    		$file->setIsLocked(true);
    		$file->setOpenedBy($currentUser);
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($file);
    		$em->flush();
    		$data['file'] = $file;
    	}
    	
    	
    	$data['roleSystemName'] = $currentUser->isAdmin() ? $roleRepo->find($roleId)->getRole() : $currentUser->getRoleSystemName();
    	 
    	$data['depots'] = $depotRepo->getOrderedDepots($depot->getCountry(), $orderBy, $currentUser, $roleId);
    	 
    	$data = $this->tryDefineRoles($data);
    
    	return $this->render('TNTMOCOAppBundle:Backend/DataEntry:index.html.twig', $data);
    }
    
    
    /*
    //public function depotAction($depotId, $fileId, $prevFileId, $reject, $complete)
    public function depotAction($depotId)
    {
    	$request = $this->getRequest();
    	$fileId = $request->request->get('fileId', 0);
    	$prevFileId = $request->request->get('prevFileId', 0);
    	$reject = $request->request->get('reject', false);
    	$complete = $request->request->get('complete', false);    	
    	$orderBy = $request->request->get('orderBy');
    	$roleId =  $request->request->get('roleId', 0);
    	  	
    	$orderBy = ( empty($orderBy) ) ? $request->query->get('orderBy', 'name') : $orderBy;
    	
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
	    	if($reject){
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
    	
    	$files = $fileRepo->getFiles($this->getUser(), $usersIds, $file, $limit, $roleId);
    	
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
    	
    	$data['depots'] = $depotRepo->getOrderedDepots($depot->getCountry(), $orderBy, $this->getUser(), $roleId);
    	
    	$data = $this->tryDefineRoles($data);
    
    	return $this->render('TNTMOCOAppBundle:Backend/DataEntry:index.html.twig', $data);
    }
    */
    
    public function tryDefineRoles($data)
    {
    	if($this->getUser()->isAdmin()){	
    		$qb = $this->getDoctrine()
    			->getRepository('TNTMOCOAppBundle:Role')
    			->createQueryBuilder('r');
    		    	
    		$qb->where(
    			$qb->expr()->in('r.id', array(3,4))
    		);
    		    		 
    		$data['roles'] = $qb->getQuery()->getResult();
    	}    	
    	return $data;
    }
}





