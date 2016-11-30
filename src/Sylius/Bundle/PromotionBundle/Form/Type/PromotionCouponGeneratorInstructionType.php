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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PromotionCouponGeneratorInstructionType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', IntegerType::class, [
                'label' => 'sylius.form.promotion_coupon_generator_instruction.amount',
            ])
            ->add('codeLength', IntegerType::class, [
                'label' => 'sylius.form.promotion_coupon_generator_instruction.code_length',
            ])
            ->add('usageLimit', IntegerType::class, [
                'required' => false,
                'label' => 'sylius.form.promotion_coupon_generator_instruction.usage_limit',
            ])
            ->add('expiresAt', DateType::class, [
                'required' => false,
                'label' => 'sylius.form.promotion_coupon_generator_instruction.expires_at',
                'widget' => 'single_text',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_promotion_coupon_generator_instruction';
    }
}
