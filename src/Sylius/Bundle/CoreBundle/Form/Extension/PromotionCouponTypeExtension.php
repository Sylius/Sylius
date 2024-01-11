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

use Sylius\Bundle\PromotionBundle\Form\Type\PromotionCouponType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class PromotionCouponTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('perCustomerUsageLimit', IntegerType::class, [
                'label' => 'sylius.form.promotion_coupon.per_customer_usage_limit',
                'required' => false,
            ])
            ->add('reusableFromCancelledOrders', CheckboxType::class, [
                'label' => 'sylius.form.promotion_coupon.reusable_from_cancelled_orders',
                'required' => false,
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [PromotionCouponType::class];
    }
}
