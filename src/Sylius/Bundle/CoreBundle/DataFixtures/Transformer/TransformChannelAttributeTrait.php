<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateChannelTrait;

trait TransformChannelAttributeTrait
{
    use FindOrCreateChannelTrait;

    private function transformChannelAttribute(EventDispatcherInterface $eventDispatcher, array $attributes): array
    {
        if (\is_string($attributes['channel'])) {
            $attributes['channel'] = $this->findOrCreateChannel($eventDispatcher, ['code' => $attributes['channel']]);
        }

        return $attributes;
    }
}
