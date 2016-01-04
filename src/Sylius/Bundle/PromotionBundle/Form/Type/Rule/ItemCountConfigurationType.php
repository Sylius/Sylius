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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemCountConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('count', 'integer', array(
                'label' => 'sylius.form.rule.item_count_configuration.count',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                ),
            ))
            ->add('equal', 'choice', [
                'label' => 'sylius.form.rule.item_count_configuration.equal',
                'choices' => [
                    'sylius.form.rule.item_count_configuration.equal_or_more' => 'equal',
                    'sylius.form.rule.item_count_configuration.more_than' => 'more_than',
                    'sylius.form.rule.item_count_configuration.exactly' => 'exactly',
                    'sylius.form.rule.item_count_configuration.repeatable' => 'modulo',
                ],
                'choices_as_values' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_item_count_configuration';
    }
}
