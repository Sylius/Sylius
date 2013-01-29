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
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
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
            ->add('category', 'sylius_tax_category_choice', array(
                'label' => 'sylius.form.tax_rate.category'
            ))
            ->add('name', 'text', array(
                'label' => 'sylius.form.tax_rate.name'
            ))
            ->add('amount', 'percent', array(
                'label' => 'sylius.form.tax_rate.amount'
            ))
            ->add('includedInPrice', 'checkbox', array(
                'label' => 'sylius.form.tax_rate.included_in_price'
            ))
            ->add('calculator', 'sylius_tax_calculator_choice', array(
                'label' => 'sylius.form.tax_rate.calculator'
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
        return 'sylius_tax_rate';
    }
}
