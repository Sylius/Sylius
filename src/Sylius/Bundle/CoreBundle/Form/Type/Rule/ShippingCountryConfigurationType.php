<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Shipping country rule configuration form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingCountryConfigurationType extends AbstractType
{
    protected $validationGroups;
    protected $dataClass;

    /**
     * @param array $validationGroups Array of validation groups
     * @param type  $dataClass        Class of Country model
     */
    public function __construct(array $validationGroups, $dataClass)
    {
        $this->validationGroups = $validationGroups;
        $this->dataClass        = $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', 'sylius_entity_to_identifier', array(
                'label'       => 'sylius.form.rule.shipping_country_configuration.country',
                'empty_value' => 'sylius.form.country.select',
                'class'       => $this->dataClass,
                'identifier'  => 'id',
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
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_shipping_country_configuration';
    }
}
