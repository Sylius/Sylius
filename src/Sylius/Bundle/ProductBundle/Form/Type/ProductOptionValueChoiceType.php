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

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Resolver\AvailableProductOptionValuesResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductOptionValueChoiceType extends AbstractType
{
    public function __construct(private readonly AvailableProductOptionValuesResolverInterface $availableProductOptionValuesResolver)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options): iterable {
                    $productOption = $options['option'];
                    if (true === $options['only_available_values']) {
                        if (null === $options['product']) {
                            throw new \RuntimeException(
                                'You must specify the "product" option when "only_available_values" is true.',
                            );
                        }

                        return $this->availableProductOptionValuesResolver->resolve(
                            $options['product'],
                            $productOption,
                        );
                    }

                    return $productOption->getValues();
                },
                'choice_value' => 'code',
                'choice_label' => 'value',
                'choice_translation_domain' => false,
                'only_available_values' => false,
                'product' => null,
            ])
            ->setRequired([
                'option',
            ])
            ->addAllowedTypes('option', [ProductOptionInterface::class])
            ->addAllowedTypes('product', ['null', ProductInterface::class])
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_product_option_value_choice';
    }
}
