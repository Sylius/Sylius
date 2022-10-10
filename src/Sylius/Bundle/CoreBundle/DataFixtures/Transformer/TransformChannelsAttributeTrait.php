<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateChannelTrait;

trait TransformChannelsAttributeTrait
{
    use FindOrCreateChannelTrait;

    private function transformChannelsAttribute(EventDispatcherInterface $eventDispatcher, array $attributes): array
    {
        $channels = [];
        foreach ($attributes['channels'] as $channel) {
            if (\is_string($channel)) {
                $channel = $this->findOrCreateChannel($eventDispatcher, ['code' => $channel]);
            }
            $channels[] = $channel;
        }
        $attributes['channels'] = $channels;

        return $attributes;
    }
}
