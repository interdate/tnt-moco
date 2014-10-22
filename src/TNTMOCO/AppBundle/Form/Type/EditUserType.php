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


class EditUserType extends UserType{
	
	public function __construct($em, $user){
		parent::__construct($em, $user);		
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options){
    	parent::buildForm($builder, $options);
    	$builder->add('password', 'text', array('label' => 'Password', 'data' => '', 'required' => false));
    	//$builder->remove('password');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	parent::setDefaultOptions($resolver);
    	$resolver->setDefaults(array(
    		'validation_groups' => array('editing'),
    	));
    }
    
    
    public function modifyFormByRole(FormInterface $form, $role = null) {
    	    
    	if($role !== null){
    		 
    		if($role->getRole() == 'ROLE_COUNTRY_ADMIN'){
    			$var = 'countries';
    			$label = 'Countries';
    			$miltipleExpanded = true;
    			$data = $this->user->getCountries();
    		}
    		else{    			
    			$var = 'country';
    			$label = 'Country';
    			$miltipleExpanded = false;    			
    			$data = $this->user->getCountry();
    		}
    
    		$form->add($var, 'entity', array(
    			'label' => $label,
    			'required' => false,
    			'multiple' => $miltipleExpanded,
    			'expanded' => $miltipleExpanded,
    			'class' => 'TNTMOCOAppBundle:Country',
    			'data' => $data,
    			'property' => 'name',
    			'empty_value' => 'Choose a Country',
    			'query_builder' => function(EntityRepository $er) {
    				return $er->createQueryBuilder('c')
    				->where('c.isActive = 1');
    			},
    		));
    	}
    }
}