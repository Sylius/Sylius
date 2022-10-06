<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateCustomerByQueryStringEvent;

trait TransformCustomerAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformCustomerAttribute(array $attributes, string $attributeKey = 'customer'): array
    {
        if (\is_string($attributes[$attributeKey])) {
            $event = new FindOrCreateCustomerByQueryStringEvent($attributes[$attributeKey]);
            $this->eventDispatcher->dispatch($event);
            $attributes[$attributeKey] = $event->getCustomer();
        }

        return $attributes;
    }
}
