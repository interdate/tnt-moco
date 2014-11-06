<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use TNTMOCO\AppBundle\Entity\PdfFile;


class ReportsController extends Controller
{
    public function indexAction()
    {   
    	$userRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:User');
    	$countryRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Country');
    	$depotRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:Depot');
    	$currentUser = $this->getUser();

    	$countries = $countryRepo->findByUser($currentUser);
    	$request = $this->getRequest();
    	if(count($countries) == 1){
    		$request->request->set('country', $countries[0]->getId());
    	}
    	$depots = array();
    	$country_id = $request->get('country');
    	
    	if((int)$country_id > 0){
	    	$country = $countryRepo->find($country_id);
	    	$depots = $depotRepo->findByCountry($country);
    	}
    	
    	$query = $userRepo->getSearchQuery($request, $currentUser);
    	$users = $query->getResult();
    	//var_dump($users[0]->getLogs()[0]->getStatus()->getCode());die;
    	return $this->render('TNTMOCOAppBundle:Backend/Reports:index.html.twig', array(
    			'users' => $users,
    			'countries' => $countries,    			
    			'depots' => $depots,
    	));
    	
    }
    /*
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
    */
}





