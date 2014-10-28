<?php

namespace TNTMOCO\AppBundle\Controller\Backend;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class FilesController extends Controller
{
    public function displayAction($batchCode, $fileId)
    {	
    	$fileRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:File');
    	$file = $fileRepo->find($fileId);
    	$file->setBatchCode($batchCode);
    	return $this->render('TNTMOCOAppBundle:Backend/Files:display.html.twig', array('file' => $file));    	
    }
}



