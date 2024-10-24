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

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantMatchType;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

/**
 * We extend the item form type a bit, to add a variant select field
 * when we're adding product to cart, but not when we edit quantity in cart.
 * We'll use simple option for that, passing the product instance required by
 * variant choice type.
 */
final class CartItemTypeExtension extends AbstractTypeExtension
{
    public function __construct(private readonly int $orderItemQuantityModifierLimit)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('quantity', IntegerType::class, [
            'attr' => ['min' => 1],
            'label' => 'sylius.ui.quantity',
            'constraints' => [
                new Range([
                    'min' => 1,
                    'max' => $this->orderItemQuantityModifierLimit,
                    'notInRangeMessage' => 'sylius.cart_item.quantity.not_in_range',
                    'groups' => 'sylius',
                ]),
            ],
        ]);

        if (isset($options['product']) && $options['product']->hasVariants() && !$options['product']->isSimple()) {
            $type =
                Product::VARIANT_SELECTION_CHOICE === $options['product']->getVariantSelectionMethod()
                ? ProductVariantChoiceType::class
                : ProductVariantMatchType::class
            ;

            $builder->add('variant', $type, [
                'product' => $options['product'],
            ]);
        }
    }

    /**
     * We need to override this method to allow setting 'product'
     * option, by default it will be null so we don't get the variant choice
     * when creating full cart form.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined([
                'product',
            ])
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [CartItemType::class];
    }
}
