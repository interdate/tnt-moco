<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Intl\Intl;

use TNTMOCO\AppBundle\Entity\Depots;
use TNTMOCO\AppBundle\Form\Type\DepotFileType;
use TNTMOCO\AppBundle\Entity\Depot;

class DepotsController extends Controller
{
	public function indexAction()
	{	
		$message = array('error' => '', 'success' => '');
		$form = $this->createForm(new DepotFileType());
		$request = $this->getRequest();
		if ($request->isMethod('POST')) {
			$form->handleRequest($request);	
			if($form->isValid()){
				$doctrine = $this->getDoctrine();
				$depotsRepo = $doctrine->getRepository('TNTMOCOAppBundle:Depot');				
				$message = $depotsRepo->saveDepots($request->files,$this->get('phpexcel'),$this->getUser()->getId());				
			}
		}
		
		return $this->render('TNTMOCOAppBundle:Backend/Depots:index.html.twig', array('form' => $form->createView(), 'message' => $message));
	}
	
}


