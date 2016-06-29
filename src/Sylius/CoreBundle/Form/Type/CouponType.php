<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\Form\Type;

use Sylius\PromotionBundle\Form\Type\CouponType as BaseCouponType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Coupon form.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class CouponType extends BaseCouponType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('perCustomerUsageLimit', 'integer', [
                'label' => 'sylius.form.coupon.per_customer_usage_limit',
            ])
        ;
    }
}
