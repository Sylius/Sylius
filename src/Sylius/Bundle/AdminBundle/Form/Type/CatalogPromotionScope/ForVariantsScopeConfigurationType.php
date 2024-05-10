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

use Sylius\Bundle\AdminBundle\Form\Type\ProductVariantAutocompleteType;
use Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForVariantsScopeConfigurationType as BaseForVariantsScopeConfigurationType;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class ForVariantsScopeConfigurationType extends AbstractType
{
    /** @param DataTransformerInterface<ProductVariantInterface, string|null> $productVariantsToCodesTransformer */
    public function __construct(private readonly DataTransformerInterface $productVariantsToCodesTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('variants', ProductVariantAutocompleteType::class, [
                'label' => 'sylius.ui.variants',
                'multiple' => true,
                'required' => false,
            ])
            ->get('variants')->addModelTransformer($this->productVariantsToCodesTransformer)
        ;
    }

    public function getParent(): string
    {
        return BaseForVariantsScopeConfigurationType::class;
    }
}
