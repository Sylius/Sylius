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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShippingCategoryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxCategoryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;

final class ShippingMethodTransformer implements ShippingMethodTransformerInterface
{
    use TransformNameToCodeAttributeTrait;
    use TransformZoneAttributeTrait;
    use TransformTaxCategoryAttributeTrait;
    use TransformChannelsAttributeTrait;

    public function __construct(
        private ZoneFactoryInterface $zoneFactory,
        private TaxCategoryFactoryInterface $taxCategoryFactory,
        private ShippingCategoryFactoryInterface $shippingCategoryFactory,
        private ChannelFactoryInterface $channelFactory,
    ) {
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
            $attributes['category'] = $this->shippingCategoryFactory::findOrCreate(['code' => $attributes['category']]);
        }

        return $attributes;
    }
}
