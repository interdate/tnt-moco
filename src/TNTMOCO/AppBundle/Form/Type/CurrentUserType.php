<?php

namespace TNTMOCO\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use TNTMOCO\AppBundle\Entity\Country;


class CurrentUserType extends EditUserType{
	
	public function __construct($em, $user){
		parent::__construct($em, $user);		
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options){
    	parent::buildForm($builder, $options);
    	$builder->remove('role');
    	$builder->remove('country');
    	$builder->remove('depot');
    	
    	$builder->add('password', 'text', array('label' => 'New Password', 'data' => '', 'required' => false));
    	$builder->add('oldPassword', 'text', array('label' => 'Old Password', 'data' => '', 'required' => false));
    }
    
}