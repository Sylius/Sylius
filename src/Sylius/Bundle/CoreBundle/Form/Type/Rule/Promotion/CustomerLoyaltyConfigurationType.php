<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Rule\Promotion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CustomerLoyaltyConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('time', 'integer', array(
                'label'       => 'sylius.form.rule.customer_loyalty_configuration.time',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('unit', 'choice', array(
                'label'       => 'sylius.form.rule.customer_loyalty_configuration.unit.header',
                'choices'     => array(
                    'days'   => 'sylius.form.rule.customer_loyalty_configuration.unit.days',
                    'weeks'  => 'sylius.form.rule.customer_loyalty_configuration.unit.weeks',
                    'months' => 'sylius.form.rule.customer_loyalty_configuration.unit.months',
                    'years'  => 'sylius.form.rule.customer_loyalty_configuration.unit.years',
                ),
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('after', 'checkbox', array(
                'label' => 'sylius.form.rule.customer_loyalty_configuration.after',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_customer_loyalty_configuration';
    }
}
