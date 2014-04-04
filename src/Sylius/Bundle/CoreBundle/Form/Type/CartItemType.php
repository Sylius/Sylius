<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\CartBundle\Form\Type\CartItemType as BaseCartItemType;
use Sylius\Component\Core\Model\Product;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * We extend the item form type a bit, to add a variant select field
 * when we're adding product to cart, but not when we edit quantity in cart.
 * We'll use simple option for that, passing the product instance required by
 * variant choice type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartItemType extends BaseCartItemType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if (isset($options['product']) && $options['product']->hasVariants()) {
            $type = Product::VARIANT_SELECTION_CHOICE === $options['product']->getVariantSelectionMethod() ? 'sylius_product_variant_choice' : 'sylius_product_variant_match';

            $builder->add('variant', $type, array(
                'variable'  => $options['product']
            ));
        }
    }

    /**
     * We need to override this method to allow setting 'product'
     * option, by default it will be null so we don't get the variant choice
     * when creating full cart form.
     *
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setOptional(array(
                'product'
            ))
            ->setAllowedTypes(array(
                'product' => array('Sylius\Component\Core\Model\ProductInterface')
            ))
        ;
    }
}
