<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle\Form\Type;

use JMS\TranslationBundle\Annotation\Ignore;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

class GoalType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.affiliate_goal.name',
            ))
            ->add('description', 'text', array(
                'label' => 'sylius.form.affiliate_goal.description',
            ))
            ->add('startsAt', 'datetime', array(
                'label' => 'sylius.form.affiliate_goal.starts_at',
                'empty_value' =>/** @Ignore */ array('year' => '-', 'month' => '-', 'day' => '-'),
                'time_widget' => 'text',
            ))
            ->add('endsAt', 'datetime', array(
                'label' => 'sylius.form.affiliate_goal.ends_at',
                'empty_value' =>/** @Ignore */ array('year' => '-', 'month' => '-', 'day' => '-'),
                'time_widget' => 'text',
            ))
            ->add('rules', 'sylius_affiliate_goal_rule_collection', array(
                'label' => 'sylius.form.affiliate_goal.rules',
                'button_add_label' => 'sylius.affiliate_goal.add_rule',
            ))
            ->add('actions', 'sylius_affiliate_goal_action_collection', array(
                'label' => 'sylius.form.affiliate_goal.actions',
                'button_add_label' => 'sylius.affiliate_goal.add_action',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_affiliate_goal';
    }
}
