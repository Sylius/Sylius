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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CatalogPromotionActionFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CatalogPromotionScopeFactoryInterface;

final class CatalogPromotionTransformer implements CatalogPromotionTransformerInterface
{
    use TransformNameToCodeAttributeTrait;
    use TransformChannelsAttributeTrait;
    use TransformCatalogPromotionActionsAttributeTrait;
    use TransformCatalogPromotionScopesAttributeTrait;

    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformNameToCodeAttribute($attributes);
        $attributes = $this->transformChannelsAttribute($this->eventDispatcher, $attributes);
        $attributes = $this->transformActionsAttribute($attributes);
        $attributes = $this->transformScopesAttribute($this->eventDispatcher, $attributes);

        if (null === $attributes['label']) {
            $attributes['label'] = $attributes['name'];
        }

        if (\is_string($attributes['start_date'])) {
            $attributes['start_date'] = new \DateTimeImmutable($attributes['start_date']);
        }

        if (\is_string($attributes['end_date'])) {
            $attributes['end_date'] = new \DateTimeImmutable($attributes['end_date']);
        }

        return $attributes;
    }
}
