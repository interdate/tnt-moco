<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * ImageFile
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TNTMOCO\AppBundle\Entity\ImageFileRepository")
 */

class ImageFile extends File
{	
	/**
	 * ImageFile file
	 *
	 * @var File
	 *
	 * @Assert\File(
	 *     maxSize = "5M",
	 *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
	 *     maxSizeMessage = "The maxmimum allowed file size is 5MB.",
	 *     mimeTypesMessage = "Only the filetypes image are allowed."
	 * )
	 */
	protected $file;	    
    
    public function getUploadDir()
    {	
    	$uploadDir = '/docs/pickup/images/' . $this->user->getId();
    	if(!is_dir($uplodDir)){
    		mkdir($uploadDir);
    	}    		
    		
    	return $uploadDir;
    }
}
