<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;

trait TransformChannelAttributeTrait
{
    private ChannelFactoryInterface $channelFactory;

    private function transformChannelAttribute(array $attributes): array
    {
        if (\is_string($attributes['channel'])) {
            $attributes['channel'] = $this->channelFactory::findOrCreate(['code' => $attributes['channel']]);
        }

        return $attributes;
    }
}
