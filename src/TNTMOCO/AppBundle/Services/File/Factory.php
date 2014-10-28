<?php

namespace TNTMOCO\AppBundle\Services\File;

use TNTMOCO\AppBundle\Entity\ImageFile;
use TNTMOCO\AppBundle\Entity\TextFile;
use TNTMOCO\AppBundle\Entity\PdfFile;

class Factory 
{
	public static function create($type){		
		switch ($type){
			case 'PU':
				return new ImageFile($type);
				break;
				
			case 'DL':
			case 'PP':
				return new TextFile($type);
				break;
				
			case 'CN':
				return new PdfFile($type);
				break;
				
		}		
	}
}