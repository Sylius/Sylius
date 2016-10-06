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

use Sylius\Bundle\OrderBundle\Form\Type\OrderType as BaseOrderType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('shippingAddress', 'sylius_address')
            ->add('billingAddress', 'sylius_address')
            ->add('promotionCoupon', 'sylius_promotion_coupon_to_code', [
                'by_reference' => false,
                'label' => 'sylius.form.cart.coupon',
                'required' => false,
            ])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        /** @var OptionsResolver $resolver */
        parent::setDefaultOptions($resolver);

        $resolver->setDefault('validation_groups', function (FormInterface $form) {
            $validationGroups = $this->validationGroups;

            if ((bool) $form->get('promotionCoupon')->getNormData()) { // Validate the coupon if it was sent
                $validationGroups[] = 'sylius_promotion_coupon';
            }

            return $validationGroups;
        });
    }
}
