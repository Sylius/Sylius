<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;

trait TransformProductAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformProductAttribute(array $attributes, string $attributeKey = 'product'): array
    {
        if (\is_string($attributes[$attributeKey])) {
            $event = new FindOrCreateResourceEvent(ProductFactoryInterface::class, ['code' => $attributes[$attributeKey]]);
            $this->eventDispatcher->dispatch($event);

            $attributes[$attributeKey] = $event->getResource();
        }

        return $attributes;
    }
}
