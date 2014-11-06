<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TNTMOCO\AppBundle\Entity\LogRepository")
 */
class Log
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
     * @ORM\Column(name="file_name", type="string", length=255)
     */
    private $fileName;
    
    /**
    * @ORM\ManyToOne(targetEntity="TypeLogs")
    * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
    */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="StatusLogs")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


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
     * Set fileName
     *
     * @param string $fileName
     * @return Log
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->fileName;
    }

   
    /**
     * Set type
     *
     * @param \TNTMOCO\AppBundle\Entity\TypeLogs $type
     * @return Log
     */
    public function setType(\TNTMOCO\AppBundle\Entity\TypeLogs $type = null)
    {
        $this->type = $type;

        return $this;
    }
    
    /**
     * Get type
     *
     * @return \TNTMOCO\AppBundle\Entity\TypeLogs
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Log
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set status
     *
     * @param \TNTMOCO\AppBundle\Entity\StatusLogs $status
     * @return Log
     */
    public function setStatus(\TNTMOCO\AppBundle\Entity\StatusLogs $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \TNTMOCO\AppBundle\Entity\StatusLogs 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user
     *
     * @param \TNTMOCO\AppBundle\Entity\User $user
     * @return Log
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
}
