<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * TextFile
 *
 * @ORM\Table()
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
    
    public function getUploadDir()
    {    	
    	return '/docs/pickup/pdf';
    }
}
