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

namespace Sylius\Bundle\AdminBundle\Form\Extension;

use Sylius\Bundle\PromotionBundle\Form\Type\PromotionRuleType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class PromotionTypeExtension extends AbstractTypeExtension
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rules', LiveCollectionType::class, [
                'entry_type' => PromotionRuleType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_options' => [
                    'label' => 'sylius.ui.add_rule',
                ],
            ])
        ;
    }

    /** @return iterable<class-string> */
    public static function getExtendedTypes(): iterable
    {
        return [PromotionType::class];
    }
}
