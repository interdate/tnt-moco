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


class UserType extends AbstractType{
	
	protected $em;

	public function __construct($em){
		$this->em = $em;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options){
    	
    	$builder->add('username', 'text', array('label' => 'Username'));
    	$builder->add('password', 'text', array('label' => 'Password'));
    	
    	$builder->add('role', 'entity', array(
    		'label' => 'Role',
    		'multiple' => false,
    		'expanded' => false,    		
    		'class' => 'TNTMOCOAppBundle:Role',
    		'property' => 'name',
    		'query_builder' => function(EntityRepository $er) {
    			return $er->createQueryBuilder('r')
    			->where('r.id > 1');
    		},
    	));
    	
    	
    	$builder->add('firstName', 'text', array('label' => 'First Name'));
    	$builder->add('lastName', 'text', array('label' => 'Last Name'));    	
    	$builder->add('email', 'text', array('label' => 'E-mail'));
    	$builder->add('phone', 'text', array('label' => 'Phone'));
    	
    	
    	$builder->add('country', 'entity', array(
    		'label' => 'Country',
    		'required' => false,
    		'multiple' => true,
    		'expanded' => true,
    		'class' => 'TNTMOCOAppBundle:Country',
    		'property' => 'name',	
    		'query_builder' => function(EntityRepository $er) {
    			return $er->createQueryBuilder('c')
    			->where('c.isActive = 1');
    		},
    		
    	));

    	$builder->add('pickup', 'checkbox', array(
    		'label' => 'Pick Up',
    		'required' => false
    	));
    	    	
    	$modifyFormByRole = function (FormInterface $form, $role = null) {
    		    		
    		if($role !== null){    		
	    		$miltipleExpanded = $role->getRole() == 'ROLE_COUNTRY_ADMIN' ? true : false;	    		
	    		
	    		$form->add('country', 'entity', array(
	    			'label' => 'Country',
	    			'required' => false,
	    			'multiple' => $miltipleExpanded,
	    			'expanded' => $miltipleExpanded,
	    			'class' => 'TNTMOCOAppBundle:Country',
	    			'property' => 'name',
	    			'empty_value' => 'Choose a Country',
	    			'query_builder' => function(EntityRepository $er) {
	    				return $er->createQueryBuilder('c')
	    				->where('c.isActive = 1');
	    			},
	    		));	    		
    		}
    	};
    	
    	
    	
    	$modifyFormByCountry = function (FormInterface $form, $country = null) {
    		
    		$depots = (!$country instanceof ArrayCollection && !$country instanceof  PersistentCollection && null !== $country) ? $country->getDepots() : array();
    		
    		$form->add('depot', 'entity', array(
    			'class'       => 'TNTMOCOAppBundle:Depot',
    			'required' => false,
    			'empty_value' => 'Choose a Depot',    			
    			'choices'     => $depots,
    		));
    	};
    	
    	
    	
    	$builder->get('role')->addEventListener(
    		FormEvents::POST_SUBMIT,
    		function (FormEvent $event) use ($modifyFormByRole) {
    			// It's important here to fetch $event->getForm()->getData(), as
    			// $event->getData() will get you the client data (that is, the ID)
    			$role = $event->getForm()->getData();
    				 
    			// since we've added the listener to the child, we'll have to pass on
    			// the parent to the callback functions!
    			$modifyFormByRole($event->getForm()->getParent(), $role);
    		}
    	);
    	
    	
    	$builder->get('country')->addEventListener(
    		FormEvents::POST_SUBMIT,
    		function (FormEvent $event) use ($modifyFormByCountry) {
    			$country = $this->em->getRepository('TNTMOCOAppBundle:Country')->find($event->getData());
    			$modifyFormByCountry($event->getForm()->getParent(), $country);
    		}
    	);
    	
    	$builder->addEventListener(
    		FormEvents::PRE_SET_DATA,
    		function (FormEvent $event) use ($modifyFormByRole, $modifyFormByCountry) {
    			// this would be your entity, i.e. User
    			$data = $event->getData();
    			$form = $event->getForm();
    			
    			$modifyFormByRole($form, $data->getRole());    			
    			$modifyFormByCountry($form, $data->getCountries());
    		}
    	);
    	
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$resolver->setDefaults(array(
    		'csrf_protection' => false,
    	));
    }
    
    public function getName(){
        return 'user';
    }
}




/*


class SportMeetupType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('sport', 'entity', array(
				'class'       => 'AcmeDemoBundle:Sport',
				'empty_value' => '',
		));
		;

		$formModifier = function (FormInterface $form, Sport $country = null) {
			$depots = null === $country ? array() : $country->getAvailableDepots();

			$form->add('Depot', 'entity', array(
					'class'       => 'AcmeDemoBundle:Depot',
					'empty_value' => '',
					'choices'     => $depots,
			));
		};

		$builder->addEventListener(
				FormEvents::PRE_SET_DATA,
				function (FormEvent $event) use ($formModifier) {
					// this would be your entity, i.e. SportMeetup
					$data = $event->getData();

					$formModifier($event->getForm(), $data->getSport());
				}
		);

		$builder->get('sport')->addEventListener(
				FormEvents::POST_SUBMIT,
				function (FormEvent $event) use ($formModifier) {
					// It's important here to fetch $event->getForm()->getData(), as
					// $event->getData() will get you the client data (that is, the ID)
					$country = $event->getForm()->getData();

					// since we've added the listener to the child, we'll have to pass on
					// the parent to the callback functions!
					$formModifier($event->getForm()->getParent(), $country);
				}
		);
	}

	// ...
}

*/
