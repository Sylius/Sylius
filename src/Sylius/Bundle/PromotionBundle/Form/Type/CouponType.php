<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Promotion\Model\CouponInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CouponType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', [
                'label'   => 'sylius.form.coupon.type',
                'choices' => [
                    CouponInterface::TYPE_COUPON    => 'sylius.form.coupon.types.coupon',
                    CouponInterface::TYPE_GIFT_CARD => 'sylius.form.coupon.types.gift_card',
                ],
            ])
            ->add('code', 'text', [
                'label' => 'sylius.form.coupon.code',
            ])
            ->add('amount', 'sylius_money', [
                'label' => 'sylius.form.coupon.amount',
            ])
            ->add('usageLimit', 'integer', [
                'label' => 'sylius.form.coupon.usage_limit',
            ])
            ->add('expiresAt', 'date', [
                'label' => 'sylius.form.coupon.expires_at',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_coupon';
    }
}
