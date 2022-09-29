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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxCategoryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;

final class TaxRateTransformer implements TaxRateTransformerInterface
{
    use TransformNameToCodeAttributeTrait;
    use TransformZoneAttributeTrait;

    public function __construct(
        private ZoneFactoryInterface $zoneFactory,
        private TaxCategoryFactoryInterface $taxCategoryFactory,
    ) {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformNameToCodeAttribute($attributes);
        $attributes = $this->transformZoneAttribute($attributes);

        if (is_string($attributes['category'])) {
            $attributes['category'] = $this->taxCategoryFactory::randomOrCreate(['code' => $attributes['category']]);
        }

        return $attributes;
    }
}
