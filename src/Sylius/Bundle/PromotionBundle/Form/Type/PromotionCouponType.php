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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class PromotionCouponType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usageLimit', IntegerType::class, [
                'label' => 'sylius.form.promotion_coupon.usage_limit',
                'required' => false,
            ])
            ->add('expiresAt', DateType::class, [
                'label' => 'sylius.form.promotion_coupon.expires_at',
                'widget' => 'single_text',
                'placeholder' => ['year' => '-', 'month' => '-', 'day' => '-'],
                'required' => false,
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_promotion_coupon';
    }
}
