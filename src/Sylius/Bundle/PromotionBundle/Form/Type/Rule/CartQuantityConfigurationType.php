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

use Sylius\Component\Promotion\Checker\Comparison\ComparisonOperatorMatcher;
use Sylius\Component\Promotion\Checker\Comparison\ComparisonOperatorMatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class CartQuantityConfigurationType extends AbstractType
{
    public function __construct(private ?ComparisonOperatorMatcherInterface $comparisonOperatorMatcher = null)
    {
        if (null === $this->comparisonOperatorMatcher) {
            trigger_deprecation(
                'sylius/promotion-bundle',
                '2.3',
                'Not passing a "%s" to "%s" is deprecated and will be required in Sylius 3.0.',
                ComparisonOperatorMatcherInterface::class,
                self::class,
            );
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('count', IntegerType::class, [
                'label' => 'sylius.form.promotion_rule.cart_quantity_configuration.count',
            ])
            ->add('comparison_operator', ChoiceType::class, [
                'label' => 'sylius.form.promotion_rule.cart_quantity_configuration.comparison_operator.label',
                'choices' => $this->buildTranslatedChoices('sylius.form.promotion_rule.cart_quantity_configuration.comparison_operator.choices.'),
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_promotion_rule_cart_quantity_configuration';
    }

    /**
     * @return array<string, string>
     */
    private function buildTranslatedChoices(string $translationKeyPrefix): array
    {
        $choices = [];
        foreach ($this->getComparisonOperatorMatcher()->getAvailableComparisonOperators() as $name => $operator) {
            $choices[$translationKeyPrefix . $name] = $operator;
        }

        return $choices;
    }

    private function getComparisonOperatorMatcher(): ComparisonOperatorMatcherInterface
    {
        return $this->comparisonOperatorMatcher ??= new ComparisonOperatorMatcher();
    }
}
