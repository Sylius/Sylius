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
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Coupon generate instruction type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CouponGenerateInstructionType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'integer', array(
                'label' => 'sylius.form.coupon_generate_instruction.amount',
            ))
            ->add('usageLimit', 'integer', array(
                'label' => 'sylius.form.coupon_generate_instruction.usage_limit',
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_promotion_coupon_generate_instruction';
    }
}
