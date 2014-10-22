<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
//use JMS\Serializer\Tests\Fixtures\Log;

/**
 * LogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LogRepository extends EntityRepository
{
	public function saveLog($fileName, $type, $status, $user_id = null, $errors = null){
		$typeLog = $this->getTypeLog($type);
		$statusLog = $this->getStatusLog($status);
		if($this->existLog($fileName, $typeLog, $statusLog, $user_id)){
			$this->updateLog($fileName, $typeLog, $statusLog, $user_id, $errors);
		}else{
			$this->addLog($fileName, $typeLog, $statusLog, $user_id, $errors);
		}
	}
	
	public function getTypeLog($type){
		if($type == '' or $type == null){
			$typeLog = null;
		}else{
			$statusLogsRepo = $this->getEntityManager()->getRepository('TNTMOCOAppBundle:TypeLogs');
			$typeLog = $statusLogsRepo->findOneByCode($type);
		}
		return $typeLog;
	}
	
	public function addTypeLog($name, $code){
		$em = $this->_em;
		$typeLog = new TypeLogs();
		$typeLog->setName($name);
		$typeLog->setCode($code);
		$em->persist($typeLog);
		$em->flush();
		return $statusLog;
	}
	
	public function getStatusLog($name){
		if($name == ''){$name = null;}
		if($name != null){
			$statusLogsRepo = $this->getEntityManager()->getRepository('TNTMOCOAppBundle:StatusLogs');
			$statusLog = $statusLogsRepo->findOneByName($name);
			if(!$statusLog){
				$statusLog = $this->addStatusLog($name);				
			}
		}else{
			$statusLog = null;
		}
		return $statusLog;
	}
	
	public function addStatusLog($name){
		$em = $this->_em;
		$statusLog = new StatusLogs();
		$statusLog->setName($name);
		$em->persist($statusLog);
		$em->flush();
		return $statusLog;
	}
	
	public function existLog($fileName, $typeLog, $statusLog, $user_id){
		return ($this->findOneBy(
					array(	
							'fileName' => $fileName, 
							'type' => $typeLog->getId(), 
							//'status_id' => $statusLog->getId(), 
							'user'=>$user_id							
					)
				)) ? true : false;
	}
	
	public function updateLog($fileName, $typeLog, $statusLog, $user_id, $errors = null){
		if($typeLog->getCode() == 'DP'){
			$this->addLog($fileName, $typeLog, $statusLog, $user_id, $errors);
		}
		$em = $this->_em;
		$log = $this->findOneBy(
					array(	
							'fileName' => $fileName, 
							'type' => $typeLog->getId(), 
							//'status_id' => $statusLog->getId(), 
							'user'=>$user_id							
					)
				);
		$log->setDate(new \DateTime("now"));//date('Y-m-d',strtotime("-1 day"))
		$log->setStatus($statusLog);
		$em->persist($log);
		$em->flush();
	}
	
	public function addLog($fileName, $typeLog, $statusLog, $user_id, $errors = null){
		$user = ($user_id == null) ? null : $this->getEntityManager()->getRepository('TNTMOCOAppBundle:User')->find($user_id);
		$em = $this->_em;
		$log = new Log();
		$log->setFileName($fileName);
		$log->setType($typeLog);
		$log->setDate(new \DateTime("now"));//date('Y-m-d',strtotime("-1 day"))
		$log->setStatus($statusLog);
		$log->setUser($user);
		$em->persist($log);
		$em->flush();
		if($errors != null){
			$this->addErrors($log, $errors);
		}
	}
	
	public function addErrors($log, $errors){
		$em = $this->_em;
		if(count($errors) > 0){
			foreach ($errors as $error){
				$errorLog = new ErrorLogs();
				$errorLog->setLog($log);
				$errorLog->setData($error['data']);
				$errorLog->setMessage($error['error']);
				$em->persist($errorLog);
				$em->flush();
			}
		}
	}
}