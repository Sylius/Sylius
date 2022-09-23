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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShippingCategoryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxCategoryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;

final class ShippingMethodFactoryTransformer implements ShippingMethodFactoryTransformerInterface
{
    public function __construct(
        private ZoneFactoryInterface $zoneFactory,
        private TaxCategoryFactoryInterface $taxCategoryFactory,
        private ShippingCategoryFactoryInterface $shippingCategoryFactory,
    ) {
    }

    public function transform(array $attributes): array
    {
        if (null === $attributes['code']) {
            $attributes['code'] = StringInflector::nameToCode($attributes['name']);
        }

        if (\is_string($attributes['zone'])) {
            $attributes['zone'] = $this->zoneFactory::findOrCreate(['code' => $attributes['zone']]);
        }

        if (\is_string($attributes['tax_category'])) {
            $attributes['tax_category'] = $this->taxCategoryFactory::findOrCreate(['code' => $attributes['tax_category']]);
        }

        if (\is_string($attributes['category'])) {
            $attributes['category'] = $this->shippingCategoryFactory::findOrCreate(['code' => $attributes['category']]);
        }

        return $attributes;
    }
}
