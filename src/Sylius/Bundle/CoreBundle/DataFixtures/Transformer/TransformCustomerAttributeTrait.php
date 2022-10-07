<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerFactoryInterface;

trait TransformCustomerAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformCustomerAttribute(array $attributes, string $attributeKey = 'customer'): array
    {
        if (\is_string($attributes[$attributeKey])) {
            $event = new FindOrCreateResourceEvent(CustomerFactoryInterface::class, ['email' => $attributes[$attributeKey]]);
            $this->eventDispatcher->dispatch($event);
            $attributes[$attributeKey] = $event->getResource();
        }

        return $attributes;
    }
}
