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

namespace Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

final class PercentageDiscountActionConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', PercentType::class, [
                'label' => 'sylius.ui.amount',
                'html5' => true,
                'scale' => 2,
                'constraints' => [
                    new NotBlank([
                        'groups' => 'sylius',
                        'message' => 'sylius.catalog_promotion_action.percentage_discount.not_number_or_empty',
                    ]),
                    new Range([
                        'min' => 0,
                        'max' => 1,
                        'notInRangeMessage' => 'sylius.catalog_promotion_action.percentage_discount.not_in_range',
                        'groups' => ['sylius'],
                    ]),
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_catalog_promotion_action_percentage_discount_configuration';
    }
}
