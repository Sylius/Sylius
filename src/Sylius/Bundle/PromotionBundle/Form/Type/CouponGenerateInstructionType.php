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
            ->add('amount', 'integer', [
                'label' => 'sylius.form.coupon_generate_instruction.amount',
            ])
            ->add('codeLength', 'integer', [
                'label' => 'sylius.form.coupon_generate_instruction.code_length',
            ])
            ->add('usageLimit', 'integer', [
                'required' => false,
                'label' => 'sylius.form.coupon_generate_instruction.usage_limit',
            ])
            ->add('expiresAt', 'date', [
                'required' => false,
                'label' => 'sylius.form.coupon_generate_instruction.expires_at',
                'widget' => 'single_text',
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
