<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Tax rate form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class TaxRateType extends AbstractType
{
    /**
     * Data class.
     *
     * @var string
     */
    protected $dataClass;

    /**
     * Constructor.
     *
     * @param string $dataClass
     */
    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', 'sylius_taxation_category_choice', array(
                'label' => 'sylius_taxation.label.rate.category'
            ))
            ->add('name', 'text', array(
                'label' => 'sylius_taxation.label.rate.name'
            ))
            ->add('amount', 'percent', array(
                'label' => 'sylius_taxation.label.rate.amount'
            ))
            ->add('includedInPrice', 'checkbox', array(
                'label' => 'sylius_taxation.label.rate.included_in_price'
            ))
            ->add('calculator', 'sylius_taxation_calculator_choice', array(
                'label' => 'sylius_taxation.label.rate.calculator'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_taxation_rate';
    }
}
