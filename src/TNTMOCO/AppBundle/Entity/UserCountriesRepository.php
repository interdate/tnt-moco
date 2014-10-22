<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserCountriesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserCountriesRepository extends EntityRepository
{
	public function removeUserCountries($user) 
	{	
		$em = $this->getEntityManager();
		$userCountries = $this->findByUser($user);		
		foreach ($userCountries as $userCountry){
			$em->remove($userCountry);
		}	
	}
}
