<?php

namespace TNTMOCO\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;
#use Symfony\Component\Form\FormEvent;
use Symfony\Component\Security\Core\SecurityContext;


class DepotFileType extends AbstractType{	
		
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('file','file', array(
    		'label' => 'Depots file:',
    		'data' => '',
    		'constraints' => array(
    			new Constraints\File(array(
    				'maxSize' => '2048k',
    				'mimeTypes' => array('application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
    				'mimeTypesMessage' => 'Please upload a valid file (.xls,.xlsx)'
    			))
    		)
    	));
    }
    
    public function getName()
    {
        return 'depots';
    }
}



