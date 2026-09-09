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

use Sylius\Bundle\PromotionBundle\Form\Type\PromotionCouponGeneratorInstructionType as BasePromotionCouponGeneratorInstructionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PromotionCouponGeneratorInstructionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            $trackUsage = isset($data['trackUsage']) && (bool) $data['trackUsage'];

            $event->getForm()->add('usageLimit', IntegerType::class, [
                'label' => 'sylius.form.promotion_coupon_generator_instruction.usage_limit',
                'required' => false,
                'disabled' => !$trackUsage,
            ]);
        });
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_promotion_coupon_generator_instruction';
    }

    public function getParent(): string
    {
        return BasePromotionCouponGeneratorInstructionType::class;
    }
}
