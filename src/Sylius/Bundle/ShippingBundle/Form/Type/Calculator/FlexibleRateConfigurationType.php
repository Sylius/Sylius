<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\Type\Calculator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Flexible rate calculator configuration form.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FlexibleRateConfigurationType extends AbstractType
{
    /**
     * Validation groups.
     *
     * @var array
     */
    protected $validationGroups;

    /**
     * Constructor.
     *
     * @param array $validationGroups
     */
    public function __construct(array $validationGroups)
    {
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_item_cost', 'sylius_money', array(
                'label' => 'sylius.form.shipping_calculator.flexible_rate_configuration.first_item_cost',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('additional_item_cost', 'sylius_money', array(
                'label' => 'sylius.form.shipping_calculator.flexible_rate_configuration.additional_item_cost',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('additional_item_limit', 'integer', array(
                'required' => false,
                'label' => 'sylius.form.shipping_calculator.flexible_rate_configuration.additional_item_limit',
                'constraints' => array(
                    new Type(array('type' => 'integer')),
                )
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
                'data_class'        => null,
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_shipping_calculator_flexible_rate_configuration';
    }
}
