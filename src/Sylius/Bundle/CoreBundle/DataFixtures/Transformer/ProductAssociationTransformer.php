<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateProductAssociationTypeTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateProductTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\RandomOrCreateProductAssociationTypeTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\RandomOrCreateProductTrait;

final class ProductAssociationTransformer implements ProductAssociationTransformerInterface
{
    use FindOrCreateProductTrait;
    use FindOrCreateProductAssociationTypeTrait;
    use RandomOrCreateProductTrait;
    use RandomOrCreateProductAssociationTypeTrait;
    use TransformProductAttributeTrait;

    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function transform(array $attributes): array
    {
        if (null === $attributes['type']) {
            $attributes['type'] = $this->randomOrCreateProductAssociationType($this->eventDispatcher);
        }

        if (null === $attributes['owner']) {
            $attributes['owner'] = $this->randomOrCreateProduct($this->eventDispatcher);
        }

        $attributes = $this->transformAssociationTypeAttribute($attributes);
        $attributes = $this->transformAssociatedProductsAttribute($attributes);

        return $this->transformProductAttribute($this->eventDispatcher, $attributes, 'owner');
    }

    private function transformAssociationTypeAttribute(array $attributes): array
    {
        if (\is_string($attributes['type'])) {
            $attributes['type'] = $this->findOrCreateProductAssociationType($this->eventDispatcher, ['code' => $attributes['type']]);
        }

        return $attributes;
    }

    private function transformAssociatedProductsAttribute(array $attributes): array
    {
        $products = [];
        foreach ($attributes['associated_products'] as $product) {
            if (\is_string($product)) {
                $product = $this->findOrCreateProduct($this->eventDispatcher, ['code' => $product]);
            }

            $products[] = $product;
        }

        $attributes['associated_products'] = $products;

        return $attributes;
    }
}
