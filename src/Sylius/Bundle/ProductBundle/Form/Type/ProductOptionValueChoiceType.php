<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
    /** @var AvailableProductOptionValuesResolverInterface */
    private $availableProductOptionValuesResolver;

    public function __construct(AvailableProductOptionValuesResolverInterface $availableProductOptionValuesResolver)
    {
        $this->availableProductOptionValuesResolver = $availableProductOptionValuesResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options): iterable {
                    return $this->availableProductOptionValuesResolver->resolve(
                        $options['product'],
                        $options['option']
                    );
                },
                'choice_value' => 'code',
                'choice_label' => 'value',
                'choice_translation_domain' => false,
            ])
            ->setRequired([
                'option',
                'product',
            ])
            ->addAllowedTypes('option', [ProductOptionInterface::class])
            ->addAllowedTypes('product', [ProductInterface::class])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_product_option_value_choice';
    }
}
