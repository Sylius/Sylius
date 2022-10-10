<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateProductTrait;

trait TransformProductAttributeTrait
{
    use FindOrCreateProductTrait;

    private function transformProductAttribute(EventDispatcherInterface $eventDispatcher, array $attributes, string $attributeKey = 'product'): array
    {
        if (\is_string($attributes[$attributeKey])) {
            $attributes[$attributeKey] = $this->findOrCreateProduct($eventDispatcher, ['code' => $attributes[$attributeKey]]);
        }

        return $attributes;
    }
}
