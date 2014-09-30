<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserCountries
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TNTMOCO\AppBundle\Entity\UserCountriesRepository")
 */
class UserCountries
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="countries", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */    
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="userCountries")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    private $country;


    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \TNTMOCO\AppBundle\Entity\User $user
     * @return UserCountries
     */
    public function setUser(\TNTMOCO\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \TNTMOCO\AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set country
     *
     * @param \TNTMOCO\AppBundle\Entity\Country $country
     * @return UserCountries
     */
    public function setCountry(\TNTMOCO\AppBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \TNTMOCO\AppBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }
    
    public function __toString(){
    	return ( $this->country !== null ) ? $this->country->getName() : "";
    }
}
