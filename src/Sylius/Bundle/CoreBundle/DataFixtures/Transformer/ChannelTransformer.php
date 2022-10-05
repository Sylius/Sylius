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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CurrencyFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopBillingDataFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;

final class ChannelTransformer implements ChannelTransformerInterface
{
    use TransformNameToCodeAttributeTrait;
    use TransformLocalesAttributeTrait;
    use TransformCurrenciesAttributeTrait;

    public function __construct(
        private ZoneFactoryInterface $zoneFactory,
        private ShopBillingDataFactoryInterface $shopBillingDataFactory,
        private TaxonFactoryInterface $taxonFactory,
        private CurrencyFactoryInterface $currencyFactory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformNameToCodeAttribute($attributes);
        $attributes['hostname'] = $attributes['hostname'] ?: $attributes['code'] . '.localhost';

        if (\is_string($attributes['default_tax_zone'])) {
            $attributes['default_tax_zone'] = $this->zoneFactory::findOrCreate(['code' => $attributes['default_tax_zone']]);
        }

        if (is_array($attributes['shop_billing_data'])) {
            $attributes['shop_billing_data'] = $this->shopBillingDataFactory->create($attributes['shop_billing_data']);
        }

        if (\is_string($attributes['menu_taxon'])) {
            $attributes['menu_taxon'] = $this->taxonFactory::findOrCreate(['code' => $attributes['menu_taxon']]);
        }

        $attributes = $this->transformLocalesAttribute($attributes);

        return $this->transformCurrenciesAttribute($attributes);
    }
}
