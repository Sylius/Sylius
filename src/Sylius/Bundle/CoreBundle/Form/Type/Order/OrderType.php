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

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType as BaseOrderType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderType extends BaseOrderType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('shippingAddress', AddressType::class)
            ->add('billingAddress', AddressType::class)
            ->add('promotionCoupon', 'sylius_promotion_coupon_to_code', [
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
        /** @var OptionsResolver $resolver */
        parent::configureOptions($resolver);

        $resolver->setDefault('validation_groups', function (FormInterface $form) {
            $validationGroups = $this->validationGroups;

            if ((bool) $form->get('promotionCoupon')->getNormData()) { // Validate the coupon if it was sent
                $validationGroups[] = 'sylius_promotion_coupon';
            }

            return $validationGroups;
        });
    }
}
