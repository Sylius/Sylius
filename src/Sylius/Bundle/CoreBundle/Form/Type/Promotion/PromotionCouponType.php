<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Promotion;

use Sylius\Bundle\PromotionBundle\Form\Type\PromotionCouponType as BasePromotionCouponType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Coupon form.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class PromotionCouponType extends BasePromotionCouponType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('perCustomerUsageLimit', IntegerType::class, [
                'label' => 'sylius.form.promotion_coupon.per_customer_usage_limit',
                'required' => false,
            ])
        ;
    }
}
