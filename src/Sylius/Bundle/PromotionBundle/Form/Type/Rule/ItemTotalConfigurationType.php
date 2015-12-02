<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Item total rule configuration form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemTotalConfigurationType extends AbstractType
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
            ->add('amount', 'sylius_money', array(
                'label' => 'sylius.form.rule.item_total_configuration.amount',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                ),
            ))
            ->add('equal', 'choice', [
                'label' => 'sylius.form.rule.item_total_configuration.equal',
                'choices' => [
                    'sylius.form.rule.item_total_configuration.equal_or_more' => 'equal',
                    'sylius.form.rule.item_total_configuration.more_than' => 'more_than',
                    'sylius.form.rule.item_total_configuration.exactly' => 'exactly',
                ],
                'choices_as_values' => true,
            ])
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
    public function getName()
    {
        return 'sylius_promotion_rule_item_total_configuration';
    }
}
