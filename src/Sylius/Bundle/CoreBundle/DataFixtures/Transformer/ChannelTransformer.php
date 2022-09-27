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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopBillingDataFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;

final class ChannelTransformer implements ChannelTransformerInterface
{
    public function __construct(
        private ZoneFactoryInterface $zoneFactory,
        private ShopBillingDataFactoryInterface $shopBillingDataFactory,
    ) {
    }

    public function transform(array $attributes): array
    {
        $attributes['code'] = $attributes['code'] ?: StringInflector::nameToCode($attributes['name']);
        $attributes['hostname'] = $attributes['hostname'] ?: $attributes['code'] . '.localhost';

        if (is_string($attributes['default_tax_zone'])) {
            $attributes['default_tax_zone'] = $this->zoneFactory::randomOrCreate(['code' => $attributes['default_tax_zone']]);
        }

        if (is_array($attributes['shop_billing_data'])) {
            $attributes['shop_billing_data'] = $this->shopBillingDataFactory->create($attributes['shop_billing_data']);
        }

        return $attributes;
    }
}
