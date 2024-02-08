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

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\ProductBundle\Form\DataTransformer\ProductVariantToProductOptionsTransformer;
use Sylius\Bundle\ResourceBundle\Form\Type\FixedCollectionType;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductVariantMatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new ProductVariantToProductOptionsTransformer($options['product']));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'entries' => function (Options $options) {
                    /** @var ProductInterface $product */
                    $product = $options['product'];

                    return $product->getOptions();
                },
                'entry_type' => ProductOptionValueChoiceType::class,
                'entry_name' => fn (ProductOptionInterface $productOption) => $productOption->getCode(),
                'entry_options' => fn (Options $options) => fn (ProductOptionInterface $productOption) => [
                    'label' => $productOption->getName(),
                    'option' => $productOption,
                    'only_available_values' => true,
                    'product' => $options['product'],
                ],
            ])

            ->setRequired('product')
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }

    public function getParent(): string
    {
        return FixedCollectionType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_product_variant_match';
    }
}
