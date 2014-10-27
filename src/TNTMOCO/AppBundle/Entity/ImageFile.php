<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * ImageFile
 *
 * @ORM\Table(name="File")
 * @ORM\Entity(repositoryClass="TNTMOCO\AppBundle\Entity\ImageFileRepository")
 */

class ImageFile extends File
{	
	/**
	 * @var string
	 */
	private $batchCode;
		
	public function __construct($type){
		parent::__construct($type);
	}
	
    
    public function getUploadDir()
    {	
    	return '/docs/pickup/images/' . $this->user->getId() . '/' . $this->batchCode;
    }   
    
    /**
     * Set batchCode
     *
     * @param string $batchCode
     * @return ImageFile
     */
    public function setBatchCode($batchCode)
    {
        $this->batchCode = $batchCode;

        return $this;
    }

    /**
     * Get batchCode
     *
     * @return string 
     */
    public function getBatchCode()
    {
        return $this->batchCode;
    }

}
