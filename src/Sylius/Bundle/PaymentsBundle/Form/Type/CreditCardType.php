<?php

/*
 * This file is an extension of the Sylius package.
 *
 * (c) Dylan Johnson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentsBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Credit Card Form Type
 *
 * @author Dylan Johnson
 */
class CreditCardType extends AbstractType
{		
	protected $dataClass;
	
	protected $locale;

    public function __construct($dataClass, $locale = null)
    {
        $this->dataClass = $dataClass;
        $this->locale = $locale;
    }
    
    
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
    		->add('type', 'choice', array( // TO-DO: get allowed card types, add as options
            	'label'		=> 'sylius.form.credit_card.type',
                'expanded'	=> true,
          	))
          	->add('cardholderName', 'text', array(
                'label'	=> 'sylius.form.credit_card.cardholder_name',
          	))
          	->add('number', 'number', array(
          	    'label'	=> 'sylius.form.credit_card.number',
          	))
          	->add('securityCode', 'number', array(
          	    'label'	=> 'sylius.form.credit_card.securityCode',
          	))
          	->add('expiryMonth', 'choice', array( // TO-DO: date-based selects; combine expiry into one field
          	    'label'		=> 'sylius.form.credit_card.expiryMonth',
          	    'choices'	=>	array_combine(range(1,12), range(1,12))
          	))
          	->add('expiryYear', 'choice', array( // TO-DO: date-based selects; combine expiry into one field
          	    'label'		=> 'sylius.form.credit_card.expiryYear',
          	    'choices'	=>	$this->getViableYears()
          	))
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
    	$resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass
            ))
        ;
	}
	
	/**
	 * Get years to add as choices in expiryYear
	 * 
	 * @return array
	 */
	public function getViableYears() {
		$yearChoices = array();
		$currentYear = (int)date("Y");
		
		for ($i = 0; $i <= 20; $i++) {
    		$yearChoices[$currentYear + $i] = $currentYear + $i;
		}
		
		return $yearChoices;
	}
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_credit_card';
    }
}
