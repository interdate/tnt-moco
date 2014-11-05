<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * TextFile
 *
 * @ORM\Table(name="File")
 * @ORM\Entity(repositoryClass="TNTMOCO\AppBundle\Entity\PdfFileRepository")
 */

class PdfFile extends File
{		
	/**
	 * TextFile file
	 *
	 * @var File
	 *
	 * @Assert\File(
	 *     maxSize = "5M",
	 *     mimeTypes = {"text/pdf"},
	 *     maxSizeMessage = "The maxmimum allowed file size is 5MB.",
	 *     mimeTypesMessage = "Only the pdf files are allowed."
	 * )
	 */
	protected $file;	
	
	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="pdfFiles")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 **/
	protected $user;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="is_locked", type="boolean")
	 */
	protected $isLocked = false;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="is_rejected", type="boolean")
	 */
	protected $isRejected = false;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="is_completed", type="boolean")
	 */
	protected $isCompleted = false;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="is_deleted", type="boolean")
	 */
	protected $isDeleted = false;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="openedPdfFiles")
	 * @ORM\JoinColumn(name="opened_by", referencedColumnName="id")
	 **/
	protected $openedBy;
	
	/**
	 * @ORM\ManyToOne(targetEntity="RejectionReason", inversedBy="pdfFiles")
	 * @ORM\JoinColumn(name="rejection_reason_id", referencedColumnName="id")
	 **/
	private $rejectionReason;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=1024)
	 */
	private $comments;
		
	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=1024)
	 */
	private $completedInfo;
	
	
	
	public function __construct(){
		parent::__construct('CN');
	}
    
    public function getUploadDir()
    {    	
    	return '/docs/pickup/pdf/' . $this->user->getId();
    }
   
    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     * @return PdfFile
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Get isLocked
     *
     * @return boolean 
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set isRejected
     *
     * @param boolean $isRejected
     * @return PdfFile
     */
    public function setIsRejected($isRejected)
    {
        $this->isRejected = $isRejected;

        return $this;
    }

    /**
     * Get isRejected
     *
     * @return boolean 
     */
    public function getIsRejected()
    {
        return $this->isRejected;
    }

    /**
     * Set isCompleted
     *
     * @param boolean $isCompleted
     * @return PdfFile
     */
    public function setIsCompleted($isCompleted)
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    /**
     * Get isCompleted
     *
     * @return boolean 
     */
    public function getIsCompleted()
    {
        return $this->isCompleted;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return PdfFile
     */
    public function setIsDeleted($isDeleted)
    {
    	$this->isDeleted = $isDeleted;
    
    	return $this;
    }
    
    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getIsDeleted()
    {
    	return $this->isDeleted;
    }
    
    /**
     * Set openedBy
     *
     * @param \TNTMOCO\AppBundle\Entity\User $openedBy
     * @return PdfFile
     */
    public function setOpenedBy(\TNTMOCO\AppBundle\Entity\User $openedBy = null)
    {
        $this->openedBy = $openedBy;

        return $this;
    }

    /**
     * Get openedBy
     *
     * @return \TNTMOCO\AppBundle\Entity\User 
     */
    public function getOpenedBy()
    {
        return $this->openedBy;
    }
    
    /**
     * Set rejectionReason
     *
     * @param \TNTMOCO\AppBundle\Entity\RejectionReason $rejectionReason
     * @return PdfFile
     */
    public function setRejectionReason(\TNTMOCO\AppBundle\Entity\RejectionReason $rejectionReason = null)
    {
        $this->rejectionReason = $rejectionReason;

        return $this;
    }

    /**
     * Get rejectionReason
     *
     * @return \TNTMOCO\AppBundle\Entity\RejectionReason 
     */
    public function getRejectionReason()
    {
        return $this->rejectionReason;
    }
    
    /**
     * Set comments
     *
     * @param string $comments
     * @return PdfFile
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set completedInfo
     *
     * @param string $completedInfo
     * @return PdfFile
     */
    public function setCompletedInfo($completedInfo)
    {
        $this->completedInfo = $completedInfo;

        return $this;
    }

    /**
     * Get completedInfo
     *
     * @return string 
     */
    public function getCompletedInfo()
    {
        return $this->completedInfo;
    }    
}
