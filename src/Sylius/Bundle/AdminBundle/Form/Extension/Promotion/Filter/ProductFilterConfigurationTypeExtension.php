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

namespace Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Filter;

use Sylius\Bundle\AdminBundle\Form\Type\ProductAutocompleteChoiceType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter\ProductFilterConfigurationType;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductFilterConfigurationTypeExtension extends AbstractTypeExtension
{
    /** @param DataTransformerInterface<ProductInterface, string|null> $productsToCodesTransformer */
    public function __construct(private readonly DataTransformerInterface $productsToCodesTransformer)
    {
    }

    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('products', ProductAutocompleteChoiceType::class, [
                'label' => 'sylius.form.promotion_filter.products',
                'multiple' => true,
            ])
            ->get('products')->addModelTransformer($this->productsToCodesTransformer)
        ;
    }

    /** @return iterable<class-string> */
    public static function getExtendedTypes(): iterable
    {
        return [ProductFilterConfigurationType::class];
    }
}
