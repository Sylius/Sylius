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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Promotion\Model\CouponInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Coupon generate instruction type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CouponGenerateInstructionType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'label'   => 'sylius.form.coupon_generate_instruction.type',
                'choices' => array(
                    CouponInterface::TYPE_COUPON    => 'sylius.form.coupon.types.coupon',
                    CouponInterface::TYPE_GIFT_CARD => 'sylius.form.coupon.types.gift_card',
                ),
            ))
            ->add('amount', 'integer', array(
                'label' => 'sylius.form.coupon_generate_instruction.amount'
            ))
            ->add('value', 'sylius_money', array(
                'label' => 'sylius.form.coupon_generate_instruction.value'
            ))
            ->add('usageLimit', 'integer', [
                'label' => 'sylius.form.coupon_generate_instruction.usage_limit',
            ])
            ->add('expiresAt', 'date', [
                'label' => 'sylius.form.coupon_generate_instruction.expires_at',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_coupon_generate_instruction';
    }
}
