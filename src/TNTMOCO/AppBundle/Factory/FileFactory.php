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
	
	public static function getFileRepo($type, $doctrine){
		switch ($type){
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
	
	public static function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
}