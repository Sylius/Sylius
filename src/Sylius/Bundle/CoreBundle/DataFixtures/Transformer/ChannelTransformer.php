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
use Sylius\Bundle\CoreBundle\DataFixtures\Event\CreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopBillingDataFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\RandomOrCreateLocaleTrait;

final class ChannelTransformer implements ChannelTransformerInterface
{
    use RandomOrCreateLocaleTrait;
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

        if (null === $attributes['default_locale']) {
            $attributes['default_locale'] = $this->randomOrCreateLocale();
        }

        if (\is_string($attributes['default_tax_zone'])) {
            /** @var FindOrCreateResourceEvent $event */
            $event = $this->eventDispatcher->dispatch(
                new FindOrCreateResourceEvent(ZoneFactoryInterface::class, ['code' => $attributes['default_tax_zone']])
            );

            $attributes['default_tax_zone'] = $event->getResource();
        }

        if (is_array($attributes['shop_billing_data'])) {
            /** @var CreateResourceEvent $event */
            $event = $this->eventDispatcher->dispatch(
                new CreateResourceEvent(ShopBillingDataFactoryInterface::class, $attributes['shop_billing_data'])
            );

            $attributes['shop_billing_data'] = $event->getResource();
        }

        if (\is_string($attributes['menu_taxon'])) {
            /** @var FindOrCreateResourceEvent $event */
            $event = $this->eventDispatcher->dispatch(
                new FindOrCreateResourceEvent(TaxonFactoryInterface::class, ['code' => $attributes['menu_taxon']])
            );

            $attributes['menu_taxon'] = $event->getResource();
        }

        $attributes = $this->transformLocalesAttribute($attributes);

        return $this->transformCurrenciesAttribute($attributes);
    }
}
