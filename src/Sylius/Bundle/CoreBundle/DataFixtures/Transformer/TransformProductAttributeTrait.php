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
            /** @var FindOrCreateResourceEvent $event */
            $event = $this->eventDispatcher->dispatch(
                new FindOrCreateResourceEvent(ProductFactoryInterface::class, ['code' => $attributes[$attributeKey]])
            );

            $attributes[$attributeKey] = $event->getResource();
        }

        return $attributes;
    }
}
