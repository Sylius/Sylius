<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Order;

use Sylius\Bundle\OrderBundle\Form\Type\OrderItemType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantMatchType;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * We extend the item form type a bit, to add a variant select field
 * when we're adding product to cart, but not when we edit quantity in cart.
 * We'll use simple option for that, passing the product instance required by
 * variant choice type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CartItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('quantity', IntegerType::class, [
            'attr' => ['min' => 1],
            'label' => 'sylius.ui.quantity',
            'data' => 1,
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
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefined([
                'product',
            ])
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return OrderItemType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_cart_item';
    }
}
