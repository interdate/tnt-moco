<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TNTMOCO\AppBundle\Entity\CountryRepository")
 */
class Country
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=2)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    
    /**     
     * @ORM\OneToMany(targetEntity="Depot", mappedBy="country")
     */
    private $depots;
    
    /**
     * @ORM\OneToMany(targetEntity="UserCountries", mappedBy="country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    private $userCountries;


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
     * Set code
     *
     * @param string $code
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Country
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
        
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->depots = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add depots
     *
     * @param \TNTMOCO\AppBundle\Entity\Depot $depots
     * @return Country
     */
    public function addDepot(\TNTMOCO\AppBundle\Entity\Depot $depots)
    {
        $this->depots[] = $depots;

        return $this;
    }

    /**
     * Remove depots
     *
     * @param \TNTMOCO\AppBundle\Entity\Depot $depots
     */
    public function removeDepot(\TNTMOCO\AppBundle\Entity\Depot $depots)
    {
        $this->depots->removeElement($depots);
    }

    /**
     * Get depots
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDepots()
    {
        return $this->depots;
    }

    /**
     * Add userCountries
     *
     * @param \TNTMOCO\AppBundle\Entity\UserCountries $userCountries
     * @return Country
     */
    public function addUserCountry(\TNTMOCO\AppBundle\Entity\UserCountries $userCountries)
    {
        $this->userCountries[] = $userCountries;

        return $this;
    }

    /**
     * Remove userCountries
     *
     * @param \TNTMOCO\AppBundle\Entity\UserCountries $userCountries
     */
    public function removeUserCountry(\TNTMOCO\AppBundle\Entity\UserCountries $userCountries)
    {
        $this->userCountries->removeElement($userCountries);
    }

    /**
     * Get userCountries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserCountries()
    {
        return $this->userCountries;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Country
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    
    public function __toString(){
    	return $this->name;
    }
}
