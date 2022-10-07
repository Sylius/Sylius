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
use Sylius\Bundle\CoreBundle\DataFixtures\Event\CreateShopBillingDataEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;

final class ChannelTransformer implements ChannelTransformerInterface
{
    use TransformNameToCodeAttributeTrait;
    use TransformLocalesAttributeTrait;
    use TransformCurrenciesAttributeTrait;

    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformNameToCodeAttribute($attributes);
        $attributes['hostname'] = $attributes['hostname'] ?: $attributes['code'] . '.localhost';

        if (\is_string($attributes['default_tax_zone'])) {
            $event = new FindOrCreateResourceEvent(ZoneFactoryInterface::class, ['code' => $attributes['default_tax_zone']]);
            $this->eventDispatcher->dispatch($event);

            $attributes['default_tax_zone'] = $event->getResource();
        }

        if (is_array($attributes['shop_billing_data'])) {
            $event = new CreateShopBillingDataEvent($attributes['shop_billing_data']);
            $this->eventDispatcher->dispatch($event);

            $attributes['shop_billing_data'] = $event->getShopBillingData();
        }

        if (\is_string($attributes['menu_taxon'])) {
            $event = new FindOrCreateResourceEvent(TaxonFactoryInterface::class, ['code' => $attributes['menu_taxon']]);
            $this->eventDispatcher->dispatch($event);

            $attributes['menu_taxon'] = $event->getResource();
        }

        $attributes = $this->transformLocalesAttribute($attributes);

        return $this->transformCurrenciesAttribute($attributes);
    }
}
