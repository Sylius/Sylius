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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShippingCategoryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateShippingCategoryTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\RandomOrCreateLocaleTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\RandomOrCreateZoneTrait;

final class ShippingMethodTransformer implements ShippingMethodTransformerInterface
{
    use FindOrCreateShippingCategoryTrait;
    use RandomOrCreateZoneTrait;
    use TransformNameToCodeAttributeTrait;
    use TransformZoneAttributeTrait;
    use TransformTaxCategoryAttributeTrait;
    use TransformChannelsAttributeTrait;

    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function transform(array $attributes): array
    {
        if (null === $attributes['zone']) {
            $attributes['zone'] = $this->randomOrCreateZone($this->eventDispatcher);
        }

        $attributes = $this->transformNameToCodeAttribute($attributes);
        $attributes = $this->transformZoneAttribute($this->eventDispatcher, $attributes);
        $attributes = $this->transformTaxCategoryAttribute($this->eventDispatcher, $attributes);
        $attributes = $this->transformCategoryAttributes($attributes);

        return $this->transformChannelsAttribute($this->eventDispatcher, $attributes);
    }

    private function transformCategoryAttributes(array $attributes): array
    {
        if (\is_string($attributes['category'])) {
            $attributes['category'] = $this->findOrCreateShippingCategory($this->eventDispatcher, ['code' => $attributes['category']]);
        }

        return $attributes;
    }
}
