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

namespace Sylius\Bundle\PromotionBundle\Form\Type\Action;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionFilterCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UnitFixedDiscountConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'label' => 'sylius.form.promotion_action.fixed_discount_configuration.amount',
                'currency' => $options['currency'],
            ])
            ->add('filters', PromotionFilterCollectionType::class, [
                'label' => 'sylius.form.promotion_action.filters',
                'required' => false,
                'currency' => $options['currency'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('currency')
            ->setAllowedTypes('currency', 'string')
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_promotion_action_unit_fixed_discount_configuration';
    }
}
