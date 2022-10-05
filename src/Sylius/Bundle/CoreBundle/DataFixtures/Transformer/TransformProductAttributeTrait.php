<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateProductByCodeEvent;

trait TransformProductAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformProductAttribute(array $attributes, string $attributeKey = 'product'): array
    {
        if (\is_string($attributes[$attributeKey])) {
            $event = new FindOrCreateProductByCodeEvent($attributes[$attributeKey]);
            $this->eventDispatcher->dispatch($event);

            if (!$event->isPropagationStopped()) {
                $attributes[$attributeKey] = $event->getProduct();
            }
        }

        return $attributes;
    }
}
