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

namespace Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionScope;

use Sylius\Bundle\AdminBundle\Form\Type\ProductAutocompleteType;
use Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForProductsScopeConfigurationType as BaseForProductsScopeConfigurationType;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class ForProductsScopeConfigurationType extends AbstractType
{
    /** @param DataTransformerInterface<ProductInterface, string|null> $productsToCodesTransformer */
    public function __construct(private readonly DataTransformerInterface $productsToCodesTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('products', ProductAutocompleteType::class, [
                'label' => 'sylius.ui.products',
                'multiple' => true,
                'required' => false,
            ])
            ->get('products')->addModelTransformer($this->productsToCodesTransformer)
        ;
    }

    public function getParent(): string
    {
        return BaseForProductsScopeConfigurationType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_catalog_promotion_scope_for_products_configuration';
    }
}
