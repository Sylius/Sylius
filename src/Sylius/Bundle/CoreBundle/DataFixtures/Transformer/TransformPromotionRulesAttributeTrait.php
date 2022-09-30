<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PromotionRuleFactoryInterface;

trait TransformPromotionRulesAttributeTrait
{
    private PromotionRuleFactoryInterface $promotionRuleFactory;

    private function transformRulesAttribute(array $attributes): array
    {
        $rules = [];
        foreach ($attributes['rules'] as $rule) {
            if (\is_array($rule)) {
                $rule = $this->promotionRuleFactory::new()->withAttributes($rule)->create();
            }

            $rules[] = $rule;
        }

        $attributes['rules'] = $rules;

        return $attributes;
    }
}
