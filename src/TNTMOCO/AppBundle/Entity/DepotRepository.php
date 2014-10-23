<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
//use TNTMOCO\AppBundle\Entity\Depot;
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
				$message['success'][] = 'File upload success';
				/*$name = $file->getClientOriginalName();
				 $file->move($this->getDepotsPath($_SERVER['DOCUMENT_ROOT']), 'upload_' . $name);
				 chmod($this->getDepotsPath($_SERVER['DOCUMENT_ROOT']) . 'upload_' . $name, 0777);*/
				$phpExcelObject = $exel->createPHPExcelObject($file);
				$workSheet = $phpExcelObject->getActiveSheet();
				$highestRow = $workSheet->getHighestRow();
				$highestColumnIndex = $workSheet->getHighestColumn();
				$data = array();	
				$em = $this->_em;
				for ($row = 1; $row <= $highestRow; ++$row)
				{
					$country = $countryRepo->findOneByCode($workSheet->getCellByColumnAndRow(0, $row)->getValue());
					if(!$country)
					{
						$data[] = array(
							'code' => $workSheet->getCellByColumnAndRow(0, $row)->getValue(),
							'value' => $workSheet->getCellByColumnAndRow(1, $row)->getValue(),
							'data' => $workSheet->getCellByColumnAndRow(0, $row)->getValue().'<|-|>'.$workSheet->getCellByColumnAndRow(1, $row)->getValue(),
							'error' => "don't exist country code in database"
								
						);
					}else
					{
						if($this->findby(array('country'=>$country->getId(), 'name'=>$workSheet->getCellByColumnAndRow(1, $row)->getValue())))
						{
							$data[] = array(
								'code' => $workSheet->getCellByColumnAndRow(0, $row)->getValue(),
								'value' => $workSheet->getCellByColumnAndRow(1, $row)->getValue(),
								'data' => $workSheet->getCellByColumnAndRow(0, $row)->getValue().'<|-|>'.$workSheet->getCellByColumnAndRow(1, $row)->getValue(),
								'error' => "depot exist in database"
							);
						}else
						{
							$depot = new Depot();
							$depot->setCountry($country);
							$depot->setName($workSheet->getCellByColumnAndRow(1, $row)->getValue());
							$em->persist($depot);
							$em->flush();
							$message['success'][] = 'Deports upload success';
						}
					}
				}
				if(count($data) > 0)
				{
					$message['error'] = array('message'=>'File have error Data:', 'data'=>array());//$data
				}
				
				$logRepo->saveLog($file->getClientOriginalName(), 'DP', 'Upload process succeeded', $userId, $data);
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
}
