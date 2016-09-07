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

use Sylius\Bundle\CartBundle\Form\Type\CartType as BaseCartType;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Julien Janvier <j.janvier@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CartType extends BaseCartType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('promotionCoupon', 'sylius_promotion_coupon_to_code', [
                'by_reference' => false,
                'label' => 'sylius.form.cart.coupon',
                'required' => false,
            ])
        ;
    }
}
