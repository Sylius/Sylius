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
            ->add('equal', 'choice', array(
                'label' => 'sylius.form.rule.equal',
                'choices' => [
                    'equal' => 'sylius.form.rule.equal_or_more',
                    'more_than' => 'sylius.form.rule.more_than',
                    'exactly' => 'sylius.form.rule.exactly',
                ],
            ))
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
