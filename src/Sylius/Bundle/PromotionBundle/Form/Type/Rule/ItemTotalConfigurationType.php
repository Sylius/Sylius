<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Form\Type\Rule;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class ItemTotalConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'label' => 'sylius.form.promotion_rule.item_total_configuration.amount',
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                    new Type(['type' => 'numeric', 'groups' => ['sylius']]),
                ],
                'currency' => $options['currency'],
            ])
            ->add('comparison_operator', ChoiceType::class, [
                'label' => 'sylius.form.promotion_rule.item_total_configuration.comparison_operator.label',
                'choices' => [
                    'sylius.form.promotion_rule.item_total_configuration.comparison_operator.choices.greater_than_equal' => '>=',
                    'sylius.form.promotion_rule.item_total_configuration.comparison_operator.choices.equal' => '===',
                    'sylius.form.promotion_rule.item_total_configuration.comparison_operator.choices.different' => '!==',
                    'sylius.form.promotion_rule.item_total_configuration.comparison_operator.choices.lower_than' => '<',
                    'sylius.form.promotion_rule.item_total_configuration.comparison_operator.choices.lower_than_equal' => '<=',
                    'sylius.form.promotion_rule.item_total_configuration.comparison_operator.choices.greater_than' => '>',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('currency')
            ->setAllowedTypes('currency', 'string')
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_promotion_rule_item_total_configuration';
    }
}
