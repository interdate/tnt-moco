<?php

namespace TNTMOCO\AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Intl\Intl;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use TNTMOCO\AppBundle\Entity\User;
use TNTMOCO\AppBundle\Entity\UserCountries;
use TNTMOCO\AppBundle\Entity\Country;

use TNTMOCO\AppBundle\Form\Type\UserType;
use TNTMOCO\AppBundle\Form\Type\EditUserType;
use TNTMOCO\AppBundle\Form\Type\UserPasswordType;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Tests\Fixtures\Publisher;

class TestController extends Controller
{
    public function filesAction()
    {	
    	return $this->render('TNTMOCOAppBundle:Backend/Test:files.html.twig');
    }    
}


