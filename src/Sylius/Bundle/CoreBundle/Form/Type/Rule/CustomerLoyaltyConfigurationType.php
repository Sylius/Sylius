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

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Nth order rule configuration form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CustomerLoyaltyConfigurationType extends AbstractType
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
            ->add('time', IntegerType::class, array(
                'label'       => 'sylius.form.rule.customer_loyalty_configuration.time',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('unit', ChoiceType::class, array(
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
            ->add('after', CheckboxType::class, array(
                'label' => 'sylius.form.rule.customer_loyalty_configuration.after',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
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
    public function getBlockPrefix()
    {
        return 'sylius_promotion_rule_customer_loyalty_configuration';
    }
}
