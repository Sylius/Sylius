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

use Sylius\Bundle\AdminBundle\Form\Type\AddButtonType;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionActionType;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionScopeType;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class CatalogPromotionTypeExtension extends AbstractTypeExtension
{
    /**
     * @param array<string, string> $scopeTypes
     * @param array<string, string> $actionTypes
     */
    public function __construct(
        private readonly array $scopeTypes,
        private readonly array $actionTypes,
    ) {
    }

    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('scopes', LiveCollectionType::class, [
                'entry_type' => CatalogPromotionScopeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_type' => AddButtonType::class,
                'button_add_options' => [
                    'label' => 'sylius.ui.add_scope',
                    'types' => $this->scopeTypes,
                ],
                'button_delete_options' => [
                    'label' => false,
                ],
            ])
            ->add('actions', LiveCollectionType::class, [
                'entry_type' => CatalogPromotionActionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_type' => AddButtonType::class,
                'button_add_options' => [
                    'label' => 'sylius.ui.add_action',
                    'types' => $this->actionTypes,
                ],
                'button_delete_options' => [
                    'label' => false,
                ],
            ])
        ;
    }

    /** @return iterable<class-string> */
    public static function getExtendedTypes(): iterable
    {
        yield CatalogPromotionType::class;
    }
}
