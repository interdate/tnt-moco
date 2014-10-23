<?php

namespace TNTMOCO\AppBundle\Factory;

use TNTMOCO\AppBundle\Entity\ImageFile;
use TNTMOCO\AppBundle\Entity\TextFile;
use TNTMOCO\AppBundle\Entity\PdfFile;

class FileFactory 
{
	public static function create($type, $user){		
		switch ($type){
			case 'PU':
				return new ImageFile($type, $user);
				break;
				
			case 'DL':
			case 'PP':
				return new TextFile($type, $user);
				break;
				
			case 'CN':
				return new PdfFile($type, $user);
				break;
				
		}		
	}
}