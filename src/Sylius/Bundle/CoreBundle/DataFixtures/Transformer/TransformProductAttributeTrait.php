<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateProductByStringEvent;

trait TransformProductAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformProductAttribute(array $attributes, string $attributeKey = 'product'): array
    {
        if (\is_string($attributes[$attributeKey])) {
            $event = new FindOrCreateProductByStringEvent($attributes[$attributeKey]);
            $this->eventDispatcher->dispatch($event);

            $attributes[$attributeKey] = $event->getProduct();
        }

        return $attributes;
    }
}
