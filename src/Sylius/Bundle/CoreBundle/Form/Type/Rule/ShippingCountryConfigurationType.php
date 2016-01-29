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

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingCountryConfigurationType extends AbstractType
{
    /**
     * @var string
     */
    protected $countryClass;

    /**
     * @param string $countryClass
     */
    public function __construct($countryClass)
    {
        $this->countryClass = $countryClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', 'sylius_entity_to_identifier', [
                'label' => 'sylius.form.rule.shipping_country_configuration.country',
                'empty_value' => 'sylius.form.country.select',
                'class' => $this->countryClass,
                'identifier' => 'id',
            ])
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
