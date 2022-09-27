<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

final class PromotionActionTransformer implements PromotionActionTransformerInterface
{
    public function transform(array $attributes): array
    {
        $configuration = &$attributes['configuration'];

        foreach ($configuration as $channelCode => $channelConfiguration) {
            if (isset($channelConfiguration['amount'])) {
                $configuration[$channelCode]['amount'] = (int) ($channelConfiguration['amount'] * 100);
            }

            if (isset($channelConfiguration['percentage'])) {
                $configuration[$channelCode]['percentage'] /= 100;
            }
        }

        if (isset($configuration['percentage'])) {
            $configuration['percentage'] /= 100;
        }

        return $attributes;
    }
}
