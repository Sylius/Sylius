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
            ->add('name', 'text', [
                'label' => 'sylius.form.promotion.name',
            ])
            ->add('description', 'textarea', [
                'label' => 'sylius.form.promotion.description',
                'required' => false,
            ])
            ->add('exclusive', 'checkbox', [
                'label' => 'sylius.form.promotion.exclusive',
            ])
            ->add('usageLimit', 'integer', [
                'label' => 'sylius.form.promotion.usage_limit',
                'required' => false,
            ])
            ->add('startsAt', 'datetime', [
                'label' => 'sylius.form.promotion.starts_at',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
            ->add('endsAt', 'datetime', [
                'label' => 'sylius.form.promotion.ends_at',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
            ->add('couponBased', 'checkbox', [
                'label' => 'sylius.form.promotion.coupon_based',
                'required' => false,
            ])
            ->add('rules', 'sylius_promotion_rule_collection', [
                'label' => 'sylius.form.promotion.rules',
                'button_add_label' => 'sylius.form.promotion.add_rule',
            ])
            ->add('actions', 'sylius_promotion_action_collection', [
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
