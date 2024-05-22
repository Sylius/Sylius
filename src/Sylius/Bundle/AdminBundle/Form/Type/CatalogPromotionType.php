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

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionType as BaseCatalogPromotionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

final class CatalogPromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('scopes', CollectionType::class, [
            'label' => 'sylius.ui.scopes',
            'entry_type' => CatalogPromotionScopeType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'required' => false,
        ])
        ->add('actions', CollectionType::class, [
            'label' => 'sylius.ui.actions',
            'entry_type' => CatalogPromotionActionType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'required' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_catalog_promotion';
    }

    public function getParent(): string
    {
        return BaseCatalogPromotionType::class;
    }
}
