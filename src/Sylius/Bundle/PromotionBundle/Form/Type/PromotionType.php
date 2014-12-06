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

use JMS\TranslationBundle\Annotation\Ignore;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Promotion form type.
 *
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
            ->add('name', 'text', array(
                'label' => 'sylius.form.promotion.name'
            ))
            ->add('description', 'text', array(
                'label' => 'sylius.form.promotion.description'
            ))
            ->add('exclusive', 'checkbox', array(
                'label' => 'sylius.form.promotion.exclusive'
            ))
            ->add('usageLimit', 'integer', array(
                'label' => 'sylius.form.promotion.usage_limit'
            ))
            ->add('startsAt', 'date', array(
                'label' => 'sylius.form.promotion.starts_at',
                'empty_value' => /** @Ignore */ array('year' => '-', 'month' => '-', 'day' => '-')
            ))
            ->add('endsAt', 'date', array(
                'label' => 'sylius.form.promotion.ends_at',
                'empty_value' => /** @Ignore */ array('year' => '-', 'month' => '-', 'day' => '-')
            ))
            ->add('couponBased', 'checkbox', array(
                'label' => 'sylius.form.promotion.coupon_based',
                'required' => false
            ))
            ->add('rules', 'sylius_promotion_rule_collection', array(
                'label' => 'sylius.form.promotion.rules'
            ))
            ->add('actions', 'sylius_promotion_action_collection', array(
                'label' => 'sylius.form.promotion.actions'
            ))
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
