<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TNTMOCO\AppBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable
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
     * @ORM\Column(name="username", type="string", length=20)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=80)
     */
    private $email;
    
    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     **/    
    private $role;
    
    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;
    
    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="users")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     **/
    private $country;
    
    /**     
     * @ORM\OneToMany(targetEntity="UserCountries", mappedBy="user", indexBy="userId,country_id", cascade={"persist"})               
     */
    private $countries;
    
    /**
     * @ORM\OneToMany(targetEntity="ImageFile", mappedBy="user")
     */
    private $imageFiles;
    
    /**
     * @ORM\OneToMany(targetEntity="PdfFile", mappedBy="user")
     */
    private $pdfFiles;
    
    /**
     * @ORM\OneToMany(targetEntity="PdfFile", mappedBy="openedBy")
     */
    private $openedPdfFiles;
        
    /**
     * @ORM\ManyToOne(targetEntity="Depot", inversedBy="users")
     * @ORM\JoinColumn(name="depot_id", referencedColumnName="id")
     **/
    private $depot;    
    
    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=30)
     */
    private $firstName;    
    
    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=30)
     */
    private $lastName;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20)
     */
    private $phone;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_new", type="boolean")
     */
    private $isNew = true;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive = true;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_non_locked", type="boolean")
     */
    private $isNonLocked = true;
        
    /**
     * @var boolean
     *
     * @ORM\Column(name="pickup", type="boolean")
     */
    private $pickup = false;
    
    /**
     * @var integet
     * 
     * @ORM\Column(name="logged_attempt", type="integer")
     */
    private $loggedAttempt = 0;
    
    /**
     * @ORM\OneToMany(targetEntity="Log", mappedBy="user")
     */
    private $logs;
    
    private $oldPassword;

    
    
    /**
     * Constructor
     */
    public function __construct()
    {	
    	$this->salt = md5(uniqid(null, true));
    	$this->countries = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->imageFiles = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->pdfFiles = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->logs = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getRoles()
    {
    	return array($this->role->getRole());
    }
    
    public function getRoleSystemName()
    {
    	return $this->role->getRole();
    }
    
    public function isAdmin()
    {
    	return ($this->role->getRole() == 'ROLE_SUPER_ADMIN' || $this->role->getRole() == 'ROLE_COUNTRY_ADMIN') ? true : false;
    }
    
    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
    
    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
    	return serialize(array(
    		$this->id,
    		$this->username,
    		$this->password,
    		$this->isActive,
    		// see section on salt below
    		// $this->salt,
    	));
    }
    
    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
    	list (
    		$this->id,
    		$this->username,
    		$this->password,
    		$this->isActive,
    		// see section on salt below
    		// $this->salt
    	) = unserialize($serialized);
    }
    
    
    public function __toString()
    {
    	return strval($this->id);
    }
    
    
    public function isEnabled()
    {
    	return $this->isActive;
    }
    
    public function isAccountNonExpired()
    {
    	return true;
    }
    
    public function isAccountNonLocked()
    {
    	return $this->isNonLocked;
    }
    
    public function isCredentialsNonExpired()
    {
    	return true;
    }
    
    public function isGranted($role)
    {
    	return in_array($role, $this->getRoles());
    }
    
    


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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Set role
     *
     * @param Role $role
     * @return Users
     */
    public function setRole(Role $role = null)
    {
    	$this->role = $role;
    
    	return $this;
    }
    
    /**
     * Get role
     *
     * @return Role
     */
    public function getRole()
    {
    	return $this->role;
    }
    
    /**
     * Set salt
     *
     * @param string $salt
     * @return Users
     */
    public function setSalt($salt)
    {
    	$this->salt = $salt;
    
    	return $this;
    }
    
    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
    	return $this->salt;
    }

    

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }
    
    


    /**
     * Set isNew
     *
     * @param boolean $isNew
     * @return User
     */
    public function setIsNew($isNew)
    {
        $this->isNew = $isNew;

        return $this;
    }

    /**
     * Get isNew
     *
     * @return boolean 
     */
    public function getIsNew()
    {
        return $this->isNew;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
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

    /**
     * Set isNonLocked
     *
     * @param boolean $isNonLocked
     * @return User
     */
    public function setIsNonLocked($isNonLocked)
    {
        $this->isNonLocked = $isNonLocked;

        return $this;
    }

    /**
     * Get isNonLocked
     *
     * @return boolean 
     */
    public function getIsNonLocked()
    {
        return $this->isNonLocked;
    }

    /**
     * Set pickup
     *
     * @param boolean $pickup
     * @return User
     */
    public function setPickup($pickup)
    {
        $this->pickup = $pickup;

        return $this;
    }

    /**
     * Get pickup
     *
     * @return boolean 
     */
    public function getPickup()
    {
        return $this->pickup;
    }
    
    /**
     * Set loggedAttempt
     *
     * @param boolean $loggedAttempt
     * @return User
     */
    public function setLoggedAttempt($loggedAttempt)
    {
    	$this->loggedAttempt = $loggedAttempt;
    
    	return $this;
    }
    
    /**
     * Get pickup
     *
     * @return boolean
     */
    public function getLoggedAttempt()
    {
    	return $this->loggedAttempt;
    }

    /**
     * Set country
     *
     * @param \TNTMOCO\AppBundle\Entity\Country $country
     * @return User
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

    /**
     * Set depot
     *
     * @param \TNTMOCO\AppBundle\Entity\Depot $depot
     * @return User
     */
    public function setDepot(\TNTMOCO\AppBundle\Entity\Depot $depot = null)
    {
        $this->depot = $depot;

        return $this;
    }

    /**
     * Get depot
     *
     * @return \TNTMOCO\AppBundle\Entity\Depot 
     */
    public function getDepot()
    {
        return $this->depot;
    }
    
    public function addCountry($country)
    {
        if(!$this->countries->contains($country))
    		$this->countries[] = $country;

        return $this;
    }

    public function removeCountry($country)
    {
    	if($this->countries->contains($country))
    		$this->countries->removeElement($country);
    }
    
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * Add imageFiles
     *
     * @param \TNTMOCO\AppBundle\Entity\ImageFile $imageFiles
     * @return User
     */
    public function addImageFile(\TNTMOCO\AppBundle\Entity\ImageFile $imageFiles)
    {
        $this->imageFiles[] = $imageFiles;

        return $this;
    }

    /**
     * Remove imageFiles
     *
     * @param \TNTMOCO\AppBundle\Entity\ImageFile $imageFiles
     */
    public function removeImageFile(\TNTMOCO\AppBundle\Entity\ImageFile $imageFiles)
    {
        $this->imageFiles->removeElement($imageFiles);
    }

    /**
     * Get imageFiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImageFiles()
    {
        return $this->imageFiles;
    }
    
    /**
     * Set oldPassword
     *
     * @param string $oldPassword
     * @return User
     */
    public function setOldPassword($oldPassword)
    {
    	$this->oldPassword = $oldPassword;
    
    	return $this;
    }
    
    /**
     * Get oldPassword
     *
     * @return string
     */
    public function getOldPassword()
    {
    	return $this->oldPassword;
	}

    /**
     * Add pdfFiles
     *
     * @param \TNTMOCO\AppBundle\Entity\PdfFile $pdfFiles
     * @return User
     */
    public function addPdfFile(\TNTMOCO\AppBundle\Entity\PdfFile $pdfFiles)
    {
        $this->pdfFiles[] = $pdfFiles;

        return $this;
    }

    /**
     * Remove pdfFiles
     *
     * @param \TNTMOCO\AppBundle\Entity\PdfFile $pdfFiles
     */
    public function removePdfFile(\TNTMOCO\AppBundle\Entity\PdfFile $pdfFiles)
    {
        $this->pdfFiles->removeElement($pdfFiles);
    }

    /**
     * Get pdfFiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPdfFiles()
    {	
    	return $this->pdfFiles;
    }

    /**
     * Add openedPdfFiles
     *
     * @param \TNTMOCO\AppBundle\Entity\PdfFile $openedPdfFiles
     * @return User
     */
    public function addOpenedPdfFile(\TNTMOCO\AppBundle\Entity\PdfFile $openedPdfFiles)
    {
        $this->openedPdfFiles[] = $openedPdfFiles;

        return $this;
    }

    /**
     * Remove openedPdfFiles
     *
     * @param \TNTMOCO\AppBundle\Entity\PdfFile $openedPdfFiles
     */
    public function removeOpenedPdfFile(\TNTMOCO\AppBundle\Entity\PdfFile $openedPdfFiles)
    {
        $this->openedPdfFiles->removeElement($openedPdfFiles);
    }

    /**
     * Get openedPdfFiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOpenedPdfFiles()
    {
        return $this->openedPdfFiles;
    }

    /**
     * Add logs
     *
     * @param \TNTMOCO\AppBundle\Entity\Log $logs
     * @return User
     */
    public function addLog(\TNTMOCO\AppBundle\Entity\Log $logs)
    {
        $this->logs[] = $logs;

        return $this;
    }

    /**
     * Remove logs
     *
     * @param \TNTMOCO\AppBundle\Entity\Log $logs
     */
    public function removeLog(\TNTMOCO\AppBundle\Entity\Log $logs)
    {
        $this->logs->removeElement($logs);
    }

    /**
     * Get logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLogs()
    {
        return $this->logs;
    }
}
