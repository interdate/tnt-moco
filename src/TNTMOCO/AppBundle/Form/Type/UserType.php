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
//use Symfony\Component\Form\Extension\Core\Type\CountryType;


class UserType extends AbstractType{
	
	protected $em;
	protected $user;

	public function __construct($em, $user, $currentUser = null){
		$this->em = $em;
		$this->user = $user;
		$this->currentUser = $currentUser;
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
    			->where('r.id > ' . $this->currentUser->getRole()->getId());
    		},
    	));
    	
    	$builder->add('firstName', 'text', array('label' => 'First Name'));
    	$builder->add('lastName', 'text', array('label' => 'Last Name'));    	
    	$builder->add('email', 'text', array('label' => 'E-mail'));
    	$builder->add('phone', 'text', array('label' => 'Phone'));
    	
    	$builder->add('country', 'entity', array(
    		'label' => 'Country',
    		'required' => false,
    		'multiple' => false,
    		'expanded' => false,
    		'empty_value' => 'Choose a Country',
    		'class' => 'TNTMOCOAppBundle:Country',
    		'property' => 'name',
    		'query_builder' => function(EntityRepository $er) {
    			return $er->createQueryBuilder('c')
    			->where('c.isActive = 1');
    		},
    	));
    	
    	$builder->add('countries', 'entity', array(
    		'label' => 'Countries',
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
    	 
    	
    	$builder->add('depot', 'entity', array(
    		'class'       => 'TNTMOCOAppBundle:Depot',
    		'required' => false,
    		'empty_value' => 'Choose a Depot',
    	));

    	
    	if($this->currentUser->getRoleSystemName() == 'ROLE_COUNTRY_ADMIN'){
    		    		
    		$countries = $this->em->getRepository('TNTMOCOAppBundle:Country')->findByUser($this->currentUser);
	    	if(count($countries) == 1){
	    		
	    		$country = $countries[0];
	    		$this->currentUser->setCountry($country);
	    		$builder->add('country', 'hidden', array(
	    			'label' => 'Country',
	    			'data' => $country->getId(),
	    		));
	    		
	    		//$this->modifyFormByCountry($builder->getForm(), $country);
	    	}
	    	elseif(count($countries) > 1){
	    		$builder->add('country', 'entity', array(
	    			'label' => 'Country',
	    			'required' => false,
	    			'multiple' => false,
	    			'expanded' => false,
	    			'empty_value' => 'Choose a Country',
	    			'class' => 'TNTMOCOAppBundle:Country',
	    			'property' => 'name',
	    			'query_builder' => function(EntityRepository $er) {
	    				return $er->createQueryBuilder('c')
	    				->where('c.isActive = 0');
	    			},
	    			'choices' => $countries,
	    		));	    		
	    	}
    	}    	
		
    	
    	
    	
    	
    	$builder->get('role')->addEventListener(
    		FormEvents::POST_SUBMIT,
    		function (FormEvent $event){
    			//echo "ROLE";
    			//die;
    			// It's important here to fetch $event->getForm()->getData(), as
    			// $event->getData() will get you the client data (that is, the ID)
    			$role = $event->getForm()->getData();
    			// since we've added the listener to the child, we'll have to pass on
    			// the parent to the callback functions!
    			$this->modifyFormByRole($event->getForm()->getParent(), $role);
    		}
    	);
    	
    	$builder->get('country')->addEventListener(
    		FormEvents::POST_SUBMIT,
    		function (FormEvent $event){
    			//echo "COUNTRY";
    			//die;
    			// It's important here to fetch $event->getForm()->getData(), as
    			// $event->getData() will get you the client data (that is, the ID)
    			$country = $event->getForm()->getData();
    			
    			
    			//echo $country->getName();
    			// since we've added the listener to the child, we'll have to pass on
    			// the parent to the callback functions!    			
    			$this->modifyFormByCountry($event->getForm()->getParent(), $country);
    		}
    	);    	
    	
    	$builder->addEventListener(
    		FormEvents::PRE_SET_DATA,
    		function (FormEvent $event){
    			//echo "PRE_SET_DATA";
    			// this would be your entity, i.e. User
    			$data = $event->getData();
    			$form = $event->getForm();
    			$this->modifyFormByRole($form, $data->getRole());	
    			$this->modifyFormByCountry( $form, $data->getCountry());
    			
    		}
    	);   	
    	
    	$builder->addEventListener(
    		FormEvents::SUBMIT,
    		function (FormEvent $event){    			
    			// this would be your entity, i.e. User
    			$data = $event->getData();
    			$form = $event->getForm();
    			$this->modifyFormByCountry( $form, $data->getCountry());
    		}
    	);
    	
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$resolver->setDefaults(array(
    		'csrf_protection' => false,
    		'validation_groups' => array('creation'),
    		//'validation_groups' => false,
    	));
    }
    
    public function getName(){
        return 'user';
    }
    
    
    
    public function modifyFormByRole(FormInterface $form, $role = null) {
    
    	if($role !== null){
    
    		//$miltipleExpanded = $role->getRole() == 'ROLE_COUNTRY_ADMIN' ? true : false;
    		
    		if($this->currentUser->getRoleSystemName() == 'ROLE_COUNTRY_ADMIN'){
    		
    			$countries = $this->em->getRepository('TNTMOCOAppBundle:Country')->findByUser($this->currentUser);
    			if(count($countries) == 1){
    				$country = $countries[0];
    				$this->currentUser->setCountry($country);
    				$form->add('country', 'hidden', array(
    					'label' => 'Country',
    					'data' => $country->getId(),
    				));
    				
    				$this->modifyFormByCountry($form, $country);
    			}
    			elseif(count($countries) > 1){
    				$form->add('country', 'entity', array(
    					'label' => 'Country',
    					'required' => false,
    					'multiple' => false,
    					'expanded' => false,
    					'empty_value' => 'Choose a Country',
    					'class' => 'TNTMOCOAppBundle:Country',
    					'property' => 'name',
    					'query_builder' => function(EntityRepository $er) {
    						return $er->createQueryBuilder('c')
    						->where('c.isActive = 0');
    					},
    					'choices' => $countries,
    				));
    			}
    		}
    		else{
    			
    			if($role->getRole() == 'ROLE_COUNTRY_ADMIN'){
    				$var = 'countries';
    				$label = 'Countries';
    				$miltipleExpanded = true;
    			}
    			else{
    				$var = 'country';
    				$label = 'Country';
    				$miltipleExpanded = false;
    			}
    			
    			$form->add($var, 'entity', array(
    				'label' => $label,
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
    	}
    }
    
    
    public function modifyFormByCountry(FormInterface $form, $country = null) {

    	$depots = (!$country instanceof ArrayCollection && !$country instanceof PersistentCollection && null !== $country) ? $country->getDepots() : array();
    	
    	/*
    	if($country)
    		echo $country->getName();
    	
    	
    	echo "DPOT:" . $this->user->getDepot();
    	die;
    	*/
    	
    	//echo count($depots);
    	
    	//foreach ($depots as $depot)
    		//echo $depot;
    	//die;
    	
    	$form->add('depot', 'entity', array(
    		'class' => 'TNTMOCOAppBundle:Depot',
    		'required' => false,
    		'empty_value' => 'Choose a Depot',    		
    		'data' => $this->user->getDepot(),
    		'choices' => $depots,    		
    	));
    	
    }    
    
    /*
    public function postSubmit(FormInterface $form) {
    	 
    	$form->add('depot', 'entity', array(
    		'class' => 'TNTMOCOAppBundle:Depot',
    		'required' => false,
    		'empty_value' => 'Choose a Depot',
    		'data' => $this->user->getDepot(),
    	));
    }
    */
    
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
