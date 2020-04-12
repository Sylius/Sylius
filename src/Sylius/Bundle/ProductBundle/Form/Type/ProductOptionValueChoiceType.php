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
    /** @var AvailableProductOptionValuesResolverInterface|null */
    private $availableProductOptionValuesResolver;

    public function __construct(?AvailableProductOptionValuesResolverInterface $availableProductOptionValuesResolver)
    {
        if (null === $availableProductOptionValuesResolver) {
            @trigger_error(
                'Not passing availableProductOptionValuesResolver thru constructor is deprecated in Sylius 1.8 and ' .
                'it will be removed in Sylius 2.0'
            );
        }

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
                    $productOption = $options['option'];
                    if (true === $options['only_available_values']) {
                        if (null === $options['product']) {
                            throw new \RuntimeException(
                                'You must specify the "product" option when "only_available_values" is true.'
                            );
                        }

                        if (null === $this->availableProductOptionValuesResolver) {
                            throw new \RuntimeException(
                                sprintf(
                                    'Cannot provide only available values in "%s" because a "%s" is required but ' .
                                    'none has been set.',
                                    __CLASS__,
                                    AvailableProductOptionValuesResolverInterface::class
                                )
                            );
                        }

                        return $this->availableProductOptionValuesResolver->resolve(
                            $options['product'],
                            $productOption
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
