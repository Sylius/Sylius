<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer;

final class PromotionRuleFactoryTransformer implements PromotionRuleFactoryTransformerInterface
{
    public function transform(array $attributes): array
    {
        $configuration = &$attributes['configuration'];

        foreach ($configuration as $channelCode => $channelConfiguration) {
            if (isset($channelConfiguration['amount'])) {
                $configuration[$channelCode]['amount'] = (int) ($channelConfiguration['amount'] * 100);
            }
        }

        return $attributes;
    }
}
