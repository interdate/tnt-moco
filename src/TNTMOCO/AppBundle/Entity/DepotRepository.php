<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use TNTMOCO\AppBundle\Entity\UserCountries;


/**
 * DepotRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DepotRepository extends EntityRepository
{
	public function saveDepots($files,$exel,$userId)
	{
		$message = array('error' => '', 'success' => '');
		$countryRepo = $this->getEntityManager()->getRepository('TNTMOCOAppBundle:Country');
		$logRepo = $this->getEntityManager()->getRepository('TNTMOCOAppBundle:Log');
		foreach ($files as $uploadedFile)
		{
			$file = $uploadedFile['file'];
			if($file)
			{
				//$message['success'][] = 'File upload success';
				/*$name = $file->getClientOriginalName();
				 $file->move($this->getDepotsPath($_SERVER['DOCUMENT_ROOT']), 'upload_' . $name);
				 chmod($this->getDepotsPath($_SERVER['DOCUMENT_ROOT']) . 'upload_' . $name, 0777);*/
				$phpExcelObject = $exel->createPHPExcelObject($file);
				$workSheet = $phpExcelObject->getActiveSheet();
				$highestRow = $workSheet->getHighestRow();
				$highestColumnIndex = $workSheet->getHighestColumn();
				$data = array();	
				$em = $this->_em;
				$numberDepots = 0;
				$numberSuccessDepots = 0;
				for ($row = 1; $row <= $highestRow; ++$row)
				{
					if($workSheet->getCellByColumnAndRow(0, $row)->getValue() != '' or $workSheet->getCellByColumnAndRow(1, $row)->getValue() != ''){
						$numberDepots++;
						$country = $countryRepo->findOneByCode($workSheet->getCellByColumnAndRow(0, $row)->getValue());
						if(!$country)
						{
							$data[] = array(
								'code' => $workSheet->getCellByColumnAndRow(0, $row)->getValue(),
								'value' => $workSheet->getCellByColumnAndRow(1, $row)->getValue(),
								'data' => $workSheet->getCellByColumnAndRow(0, $row)->getValue().'<|-|>'.$workSheet->getCellByColumnAndRow(1, $row)->getValue(),
								'error' => "Country code is not exists in database"
									
							);
						}else
						{
							if($this->findby(array('country'=>$country->getId(), 'name'=>$workSheet->getCellByColumnAndRow(1, $row)->getValue())))
							{
								$data[] = array(
									'code' => $workSheet->getCellByColumnAndRow(0, $row)->getValue(),
									'value' => $workSheet->getCellByColumnAndRow(1, $row)->getValue(),
									'data' => $workSheet->getCellByColumnAndRow(0, $row)->getValue().'<|-|>'.$workSheet->getCellByColumnAndRow(1, $row)->getValue(),
									'error' => "Depot is exists in database"
								);
							}else
							{
								$numberSuccessDepots++;
								$depot = new Depot();
								$depot->setCountry($country);
								$depot->setName($workSheet->getCellByColumnAndRow(1, $row)->getValue());
								$em->persist($depot);
								$em->flush();
								//$message['success'][0] = 'The depots have been uploaded successfully';
							}
						}
					}
				}
				if(count($data) > 0)
				{
					//$message['error'] = array('message'=>'The file has a data error', 'data'=>array());//$data
				}
				
				$message['success'][0] = "There are " . $numberDepots .' depots in the file. ' . $numberSuccessDepots . ' depots have been uploaded';
				$logRepo->saveLog($file->getClientOriginalName(), 'DP', 'The depots have been uploaded successfully', $userId, $data);
			}
		}
		
		return $message;
	}
	
	public function getDepotsPath($root = false)
	{
		if(!$root)
		{
			$root = 'http://' . $_SERVER['HTTP_HOST'];
		}
		
		return $root . '/bundles/tntmocoapp/depots/';
	}
	
	public function getOrderedDepots($country, $orderBy, $currentUser, $roleId)
	{	
		
		if($country != $currentUser->getCountry()){
			if(!$currentUser->isAdmin()){
				return null;
			}
			
			$roleSystemName = $currentUser->getRoleSystemName();
			if($roleSystemName == 'ROLE_COUNTRY_ADMIN'){
				
				foreach ($currentUser->getCountries() as $userCountry ){
					$userCountries[] = $userCountry->getCountry();
				}

				if(!in_array($country,  $userCountries)){
					return null;
				}				
			}
		}
		
		
		if($orderBy == 'name'){			
			$depots =  $this->findBy(
	    		array('country' => $country),
	    		array('name' => 'ASC')
	    	);
			
			foreach ($depots as $depot){
				$pdfFilesNumber = $this->calculateDepotPdfFilesNumber($depot, $currentUser, $roleId);
				$depot->setPdfFilesNumber($pdfFilesNumber);
			}			
			
			return $depots;
		}
		elseif($orderBy == 'docsNumber'){
			$depots = $this->findByCountry($country);
			foreach ($depots as $depot){
				$pdfFilesNumber = $this->calculateDepotPdfFilesNumber($depot, $currentUser, $roleId);
				$depot->setPdfFilesNumber($pdfFilesNumber);
				$depotsArr[$depot->getId()] = $depot->getPdfFilesNumber();				
			}
			
			asort($depotsArr);
			$depots = array();
			
			foreach ($depotsArr as $depotId => $pdfFilesNumber){
				$depot = $this->find($depotId);
				$depot->setPdfFilesNumber($pdfFilesNumber);
				$depots[] = $depot;			 				
			}
			return $depots;
			
		}
		
		return $depots;
		
	}
	
	public function calculateDepotPdfFilesNumber($depot, $currentUser, $roleId)
	{
		$pdfFilesNumber = 0;
		$roleRepo = $this->getEntityManager()->getRepository('TNTMOCOAppBundle:Role');
		$roleSystemName = $currentUser->isAdmin() ? $roleRepo->find($roleId)->getRole() : $currentUser->getRoleSystemName();
		$isRejected = $roleSystemName == 'ROLE_USER' ? false : true;
		
		foreach ($depot->getUsers() as $user){
			foreach ($user->getPdfFiles() as $file){
				if( (!$file->getIsLocked() || $file->getOpenedBy() == $currentUser) && $file->getIsRejected() == $isRejected  && !$file->getIsCompleted() && !$file->getIsDeleted()){
					$pdfFilesNumber++;
				}
			}
		}		
		
		return $pdfFilesNumber;		
	}
}
