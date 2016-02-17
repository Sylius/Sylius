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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FlexibleRateConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_unit_cost', 'sylius_money', [
                'label' => 'sylius.form.shipping_calculator.flexible_rate_configuration.first_unit_cost',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'integer']),
                ],
            ])
            ->add('additional_unit_cost', 'sylius_money', [
                'label' => 'sylius.form.shipping_calculator.flexible_rate_configuration.additional_unit_cost',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'integer']),
                ],
            ])
            ->add('additional_unit_limit', 'integer', [
                'required' => false,
                'label' => 'sylius.form.shipping_calculator.flexible_rate_configuration.additional_unit_limit',
                'constraints' => [
                    new Type(['type' => 'integer']),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_shipping_calculator_flexible_rate';
    }
}
