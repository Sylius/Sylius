<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType;
use Sylius\Bundle\OrderBundle\Form\Type\CartType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionCouponToCodeType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingAddress', AddressType::class)
            ->add('billingAddress', AddressType::class)
            ->add('promotionCoupon', PromotionCouponToCodeType::class, [
                'by_reference' => false,
                'label' => 'sylius.form.cart.coupon',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setNormalizer('validation_groups', function (Options $options, array $validationGroups) {
            return function (FormInterface $form) use ($validationGroups) {
                if ((bool) $form->get('promotionCoupon')->getNormData()) { // Validate the coupon if it was sent
                    $validationGroups[] = 'sylius_promotion_coupon';
                }

                return $validationGroups;
            };
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CartType::class;
    }
}
