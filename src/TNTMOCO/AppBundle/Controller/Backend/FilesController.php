<?php

namespace TNTMOCO\AppBundle\Controller\Backend;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class FilesController extends Controller
{
    public function displayAction($fileId)
    {	
    	$fileRepo = $this->getDoctrine()->getRepository('TNTMOCOAppBundle:File');
    	$file = $fileRepo->find($fileId);
    	return $this->render('TNTMOCOAppBundle:Backend/Files:display.html.twig', array('file' => $file));    	
    }
}



