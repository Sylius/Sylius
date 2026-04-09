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

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Sylius\Bundle\PromotionBundle\Form\Type\PromotionCouponType as BasePromotionCouponType;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PromotionCouponType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
            $coupon = $event->getData();

            if ($coupon instanceof PromotionCouponInterface && !$coupon->isTrackUsage()) {
                $event->getForm()->add('usageLimit', IntegerType::class, [
                    'label' => 'sylius.form.promotion_coupon.usage_limit',
                    'required' => false,
                    'disabled' => true,
                ]);

                $event->getForm()->add('perCustomerUsageLimit', IntegerType::class, [
                    'label' => 'sylius.form.promotion_coupon.per_customer_usage_limit',
                    'required' => false,
                    'disabled' => true,
                ]);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            $trackUsage = isset($data['trackUsage']) && (bool) $data['trackUsage'];

            $event->getForm()->add('usageLimit', IntegerType::class, [
                'label' => 'sylius.form.promotion_coupon.usage_limit',
                'required' => false,
                'disabled' => !$trackUsage,
            ]);

            $event->getForm()->add('perCustomerUsageLimit', IntegerType::class, [
                'label' => 'sylius.form.promotion_coupon.per_customer_usage_limit',
                'required' => false,
                'disabled' => !$trackUsage,
            ]);
        });
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_promotion_coupon';
    }

    public function getParent(): string
    {
        return BasePromotionCouponType::class;
    }
}
