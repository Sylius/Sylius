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
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateTaxonByQueryStringEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateZoneByQueryStringEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CurrencyFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopBillingDataFactoryInterface;

final class ChannelTransformer implements ChannelTransformerInterface
{
    use TransformNameToCodeAttributeTrait;
    use TransformLocalesAttributeTrait;
    use TransformCurrenciesAttributeTrait;

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ShopBillingDataFactoryInterface $shopBillingDataFactory,
        private CurrencyFactoryInterface $currencyFactory,
    ) {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformNameToCodeAttribute($attributes);
        $attributes['hostname'] = $attributes['hostname'] ?: $attributes['code'] . '.localhost';

        if (\is_string($attributes['default_tax_zone'])) {
            $event = new FindOrCreateZoneByQueryStringEvent($attributes['default_tax_zone']);
            $this->eventDispatcher->dispatch($event);

            $attributes['default_tax_zone'] = $event->getZone();
        }

        if (is_array($attributes['shop_billing_data'])) {
            $attributes['shop_billing_data'] = $this->shopBillingDataFactory->create($attributes['shop_billing_data']);
        }

        if (\is_string($attributes['menu_taxon'])) {
            $event = new FindOrCreateTaxonByQueryStringEvent($attributes['menu_taxon']);
            $this->eventDispatcher->dispatch($event);

            $attributes['menu_taxon'] = $event->getTaxon();
        }

        $attributes = $this->transformLocalesAttribute($attributes);

        return $this->transformCurrenciesAttribute($attributes);
    }
}
