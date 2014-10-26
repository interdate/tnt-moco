<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * TextFile
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TNTMOCO\AppBundle\Entity\TextFileRepository")
 */

class TextFile extends File
{		
	/**
	 * TextFile file
	 *
	 * @var File
	 *
	 * @Assert\File(
	 *     maxSize = "1M",
	 *     mimeTypes = {"text/rtf"},
	 *     maxSizeMessage = "The maxmimum allowed file size is 1MB.",
	 *     mimeTypesMessage = "Only the text files are allowed."
	 * )
	 */
	protected $file;

	public function __construct($type){
		parent::__construct($type);
	}
    
    public function getUploadDir()
    {    	
    	return $this->type == 'DL' ?  '/docs/delivery/text' : '/docs/postpone/text';
    }
}
