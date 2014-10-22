<?php

namespace TNTMOCO\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class CountryType extends AbstractType{	
		
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
        	//'class' => 'TNTMOCOAppBundle:Country',
        	//'property' => 'name',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
        ));
    }
    
    public function getName(){
        return 'country';
    }
}



