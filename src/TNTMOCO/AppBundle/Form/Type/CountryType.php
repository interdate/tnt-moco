<?php

namespace TNTMOCO\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class CountryType extends AbstractType{	
		
public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'entity', array(
        	'class' => 'TNTMOCOAppBundle:Country',
        	'property' => 'name',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TNTMOCO\AppBundle\Entity\Country',
        ));
    }
    
    public function getName(){
        return 'country';
    }
}



