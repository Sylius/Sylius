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

final class ShippingMethodTransformer implements ShippingMethodTransformerInterface
{
    use TransformNameToCodeAttributeTrait;
    use TransformZoneAttributeTrait;
    use TransformTaxCategoryAttributeTrait;
    use TransformChannelsAttributeTrait;

    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformNameToCodeAttribute($attributes);
        $attributes = $this->transformZoneAttribute($attributes);
        $attributes = $this->transformTaxCategoryAttribute($attributes);
        $attributes = $this->transformCategoryAttributes($attributes);

        return $this->transformChannelsAttribute($attributes);
    }

    private function transformCategoryAttributes(array $attributes): array
    {
        if (\is_string($attributes['category'])) {
            /** @var FindOrCreateResourceEvent $event */
            $event = $this->eventDispatcher->dispatch(
                new FindOrCreateResourceEvent(ShippingCategoryFactoryInterface::class, ['code' => $attributes['category']])
            );

            $attributes['category'] = $event->getResource();
        }

        return $attributes;
    }
}
