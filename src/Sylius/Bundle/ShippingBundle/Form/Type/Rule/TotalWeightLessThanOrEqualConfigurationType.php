<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShippingBundle\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

final class TotalWeightLessThanOrEqualConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'weight',
            NumberType::class,
            [
                'label' => 'sylius.form.shipping_method_rule.weight',
            ],
        );
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_shipping_method_rule_total_weight_less_than_or_equal_configuration';
    }
}
