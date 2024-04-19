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

namespace Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionScope;

use Sylius\Bundle\AdminBundle\Form\Type\ProductVariantAutocompleteChoiceType;
use Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForVariantsScopeConfigurationType;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class ForVariantsScopeConfigurationTypeExtension extends AbstractTypeExtension
{
    /** @param DataTransformerInterface<ProductVariantInterface, string|null> $productVariantsToCodesTransformer */
    public function __construct(private readonly DataTransformerInterface $productVariantsToCodesTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('variants', ProductVariantAutocompleteChoiceType::class, [
                'label' => 'sylius.ui.variants',
                'multiple' => true,
                'required' => false,
            ])
            ->get('variants')->addModelTransformer($this->productVariantsToCodesTransformer)
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        yield ForVariantsScopeConfigurationType::class;
    }
}
