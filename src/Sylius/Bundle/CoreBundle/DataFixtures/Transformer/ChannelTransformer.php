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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopBillingDataFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateTaxonTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateZoneTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\RandomOrCreateLocaleTrait;

final class ChannelTransformer implements ChannelTransformerInterface
{
    use FindOrCreateTaxonTrait;
    use FindOrCreateZoneTrait;
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
            $attributes['default_locale'] = $this->randomOrCreateLocale($this->eventDispatcher);
        }

        if (\is_string($attributes['default_tax_zone'])) {
            $attributes['default_tax_zone'] = $this->findOrCreateZone($this->eventDispatcher, ['code' => $attributes['default_tax_zone']]);
        }

        if (is_array($attributes['shop_billing_data'])) {
            /** @var CreateResourceEvent $event */
            $event = $this->eventDispatcher->dispatch(
                new CreateResourceEvent(ShopBillingDataFactoryInterface::class, $attributes['shop_billing_data'])
            );

            $attributes['shop_billing_data'] = $event->getResource();
        }

        if (\is_string($attributes['menu_taxon'])) {
            $attributes['menu_taxon'] = $this->findOrCreateTaxon($this->eventDispatcher, ['code' => $attributes['menu_taxon']]);
        }

        $attributes = $this->transformLocalesAttribute($this->eventDispatcher, $attributes);

        return $this->transformCurrenciesAttribute($this->eventDispatcher, $attributes);
    }
}
