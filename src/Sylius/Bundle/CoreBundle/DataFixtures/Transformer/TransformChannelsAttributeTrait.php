<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;

trait TransformChannelsAttributeTrait
{
    private ChannelFactoryInterface $channelFactory;

    private function transformChannelsAttribute(array $attributes): array
    {
        $channels = [];
        foreach ($attributes['channels'] as $channel) {
            if (\is_string($channel)) {
                $channel = $this->channelFactory::findOrCreate(['code' => $channel]);
            }
            $channels[] = $channel;
        }
        $attributes['channels'] = $channels;

        return $attributes;
    }
}
