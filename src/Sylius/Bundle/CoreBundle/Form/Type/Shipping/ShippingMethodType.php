<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Shipping;

use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType as BaseShippingMethodType;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShippingMethodType extends BaseShippingMethodType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('zone', 'sylius_zone_choice', [
                'label' => 'sylius.form.shipping_method.zone',
            ])
            ->add('taxCategory', 'sylius_tax_category_choice', [
                'required' => false,
                'empty_value' => '---',
                'label' => 'sylius.form.shipping_method.tax_category',
            ])
            ->add('channels', 'sylius_channel_choice', [
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.shipping_method.channels',
            ])
        ;

        $prototypes = [
            'rules' => [],
            'calculators' => [],
        ];

        /** @var RuleCheckerInterface $checker */
        foreach ($this->checkerRegistry->all() as $type => $checker) {
            $prototypes['rules'][$type] = $builder->create('__name__', $checker->getConfigurationFormType())->getForm();
        }

        /** @var CalculatorInterface $calculator */
        foreach ($this->calculatorRegistry->all() as $name => $calculator) {
            $calculatorTypeName = sprintf('sylius_channel_based_shipping_calculator_%s', $calculator->getType());

            if (!$this->formRegistry->hasType($calculatorTypeName)) {
                continue;
            }

            $prototypes['calculators'][$name] = $builder->create('configuration', $calculatorTypeName)->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);
    }
}
