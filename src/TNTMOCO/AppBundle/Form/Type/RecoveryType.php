<?php

namespace TNTMOCO\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use TNTMOCO\AppBundle\Entity\Country;
//use Symfony\Component\Form\Extension\Core\Type\CountryType;


class RecoveryType extends AbstractType{

	protected $user;
	
	public function __construct($user = false){
		$this->user = $user;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options){
    	if($this->user){
    		$builder->add('password', 'repeated', array(
    			'type' => 'password',
    			'invalid_message' => 'The password fields must match.',
    			'first_options'  => array('label' => 'New Password', 'data' => ''),
    			'second_options' => array('label' => 'Repeat New Password', 'data' => '')
    		));
    	}else{
	    	$builder->add('email', 'text', array(
				'label' => 'Email',
				'constraints' => array(
					new Constraints\NotBlank(),
					new Constraints\Email(array(
						'message' => 'The email "{{ value }}" is not a valid email.', 
						'checkMX' => true
					))
				)
			));
    	}
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
        return 'recovery';
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
