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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'sylius.form.promotion.name',
            ])
            ->add('description', TextType::class, [
                'label' => 'sylius.form.promotion.description',
            ])
            ->add('exclusive', CheckboxType::class, [
                'label' => 'sylius.form.promotion.exclusive',
            ])
            ->add('usageLimit', IntegerType::class, [
                'label' => 'sylius.form.promotion.usage_limit',
            ])
            ->add('startsAt', DateTimeType::class, [
                'label' => 'sylius.form.promotion.starts_at',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
                'time_widget' => 'text',
            ])
            ->add('endsAt', DateTimeType::class, [
                'label' => 'sylius.form.promotion.ends_at',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
                'time_widget' => 'text',
            ])
            ->add('couponBased', CheckboxType::class, [
                'label' => 'sylius.form.promotion.coupon_based',
                'required' => false,
            ])
            ->add('rules', RuleCollectionType::class, [
                'label' => 'sylius.form.promotion.rules',
                'button_add_label' => 'sylius.form.promotion.add_rule',
            ])
            ->add('actions', ActionCollectionType::class, [
                'label' => 'sylius.form.promotion.actions',
                'button_add_label' => 'sylius.form.promotion.add_action',
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion';
    }
}
