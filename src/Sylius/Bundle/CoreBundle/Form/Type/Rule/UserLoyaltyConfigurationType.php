<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Nth order rule configuration form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class UserLoyaltyConfigurationType extends AbstractType
{
    protected $validationGroups;

    public function __construct(array $validationGroups)
    {
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('time', 'integer', array(
                'label'       => 'sylius.form.rule.user_loyalty_configuration.time',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('unit', 'choice', array(
                'label'       => 'sylius.form.rule.user_loyalty_configuration.unit.header',
                'choices'     => array(
                    'days'   => 'sylius.form.rule.user_loyalty_configuration.unit.days',
                    'weeks'  => 'sylius.form.rule.user_loyalty_configuration.unit.weeks',
                    'months' => 'sylius.form.rule.user_loyalty_configuration.unit.months',
                    'years'  => 'sylius.form.rule.user_loyalty_configuration.unit.years',
                ),
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('after', 'checkbox', array(
                'label' => 'sylius.form.rule.user_loyalty_configuration.after',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_user_loyalty_configuration';
    }
}
