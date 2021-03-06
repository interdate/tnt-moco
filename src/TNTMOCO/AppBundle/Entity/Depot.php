<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Depot
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TNTMOCO\AppBundle\Entity\DepotRepository")
 */
class Depot
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
     * @ORM\Column(name="name", type="string", length=80)
     */
    private $name;
    
    
	/**
	  * @ORM\ManyToOne(targetEntity="Country", inversedBy="depots")
	  * @ORM\JoinColumn(name="country_id", referencedColumnName="id")     
	  */
    private $country;
    
    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="depot")     
     */
    private $users;
    
    /**
     * @var integer     
     */
    private $pdfFilesNumber = 0;


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
     * Set name
     *
     * @param string $name
     * @return Depot
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
     * Set country
     *
     * @param \TNTMOCO\AppBundle\Entity\Country $country
     * @return Depot
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
    
    public function __toString()
    {
    	return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add users
     *
     * @param \TNTMOCO\AppBundle\Entity\User $users
     * @return Depot
     */
    public function addUser(\TNTMOCO\AppBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \TNTMOCO\AppBundle\Entity\User $users
     */
    public function removeUser(\TNTMOCO\AppBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
    
    public function setPdfFilesNumber($pdfFilesNumber){
    	$this->pdfFilesNumber = $pdfFilesNumber;
    }
    
    public function getPdfFilesNumber(){    	
    	
    	return $this->pdfFilesNumber;
    }
}
