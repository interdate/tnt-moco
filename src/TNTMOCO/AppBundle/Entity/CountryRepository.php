<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CountryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CountryRepository extends EntityRepository
{
	public function findByUser($user)
	{
		$countries = array();
		if($user->getRole()->getId() > 1){
			if(is_object($user->getCountry())){
				$countries[] = $user->getCountry();
			}else{
				if(count($user->getCountries()) > 0){					
					foreach($user->getCountries() as $userCountry){
						$countries[] = $userCountry->getCountry();
					}
				}
			}			
		}else{
			$countries = $this->findByIsActive(true);
		}
		return $countries;
	}
}
