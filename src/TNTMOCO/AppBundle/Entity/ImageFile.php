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
	protected $file;
	
	public function __construct($type, $user){
		parent::__construct($type, $user);
	}
    
    public function getUploadDir()
    {	
    	return '/docs/pickup/images/' . $this->user->getId();
    }
}
