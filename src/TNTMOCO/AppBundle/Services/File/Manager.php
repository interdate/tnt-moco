<?php

namespace TNTMOCO\AppBundle\Services\File;

use TNTMOCO\AppBundle\Entity\ImageFile;
use TNTMOCO\AppBundle\Entity\TextFile;
use TNTMOCO\AppBundle\Entity\PdfFile;

class Manager 
{
	public static function getFactory(){
		return new Factory();
	}
	
	public static function getFileRepo($fileType, $doctrine){
		switch ($fileType){
			case 'PU':
				return $doctrine->getRepository('TNTMOCOAppBundle:ImageFile');
				break;
		
			case 'DL':
			case 'PP':
				return $doctrine->getRepository('TNTMOCOAppBundle:TextFile');
				break;
		
			case 'CN':
				return $doctrine->getRepository('TNTMOCOAppBundle:PdfFile');
				break;
		
		}
	}
	
	public static function removeDir($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
}