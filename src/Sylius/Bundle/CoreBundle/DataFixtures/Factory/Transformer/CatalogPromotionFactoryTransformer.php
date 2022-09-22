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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer;

use Sylius\Component\Core\Formatter\StringInflector;

final class CatalogPromotionFactoryTransformer implements CatalogPromotionFactoryTransformerInterface
{
    public function transform(array $attributes): array
    {
        if (null === $attributes['code']) {
            $attributes['code'] = StringInflector::nameToCode($attributes['name']);
        }

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
