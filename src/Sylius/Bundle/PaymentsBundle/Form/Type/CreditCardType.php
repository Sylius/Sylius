<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentsBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Credit Card Form Type
 *
 * @author Dylan Johnson <eponymi.dev@gmail.com>
 */
class CreditCardType extends AbstractType
{
    protected $dataClass;
    protected $validationGroups;

    public function __construct($dataClass, array $validationGroups)
    {
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'label'    => 'sylius.form.credit_card.type',
                'expanded' => true,
              ))
              ->add('cardholderName', 'text', array(
                'label' => 'sylius.form.credit_card.cardholder_name',
              ))
              ->add('number', 'text', array(
                  'label' => 'sylius.form.credit_card.number',
              ))
              ->add('securityCode', 'text', array(
                  'label' => 'sylius.form.credit_card.security_code',
              ))
              ->add('expiryMonth', 'choice', array(
                  'label'   => 'sylius.form.credit_card.expiry_month',
                  'choices' => $this->getMonthChoices()
              ))
              ->add('expiryYear', 'choice', array(
                  'label'   => 'sylius.form.credit_card.expiry_year',
                  'choices' =>  $this->getViableYears()
              ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => $this->dataClass,
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_credit_card';
    }

    /**
     * Get years to add as choices in expiryYear
     *
     * @return array
     */
    private function getViableYears()
    {
        $yearChoices = array();
        $currentYear = (int) date("Y");

        for ($i = 0; $i <= 20; $i++) {
            $yearChoices[$currentYear + $i] = $currentYear + $i;
        }

        return $yearChoices;
    }

    /**
     * Get months to add as choices in expiryMonth
     *
     * @return array
     */
    private function getMonthChoices()
    {
        $monthChoices = array();

        foreach (range(1, 12) as $month) {
            $monthChoices[$month] = str_pad($month, 2, 0, STR_PAD_LEFT);
        }

        return $monthChoices;
    }
}
