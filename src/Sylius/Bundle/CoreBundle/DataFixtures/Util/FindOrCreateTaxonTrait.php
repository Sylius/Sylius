<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactoryInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Zenstruck\Foundry\Proxy;

trait FindOrCreateTaxonTrait
{
    /**
     * @return TaxonInterface|Proxy
     */
    private function findOrCreateTaxon(EventDispatcherInterface $eventDispatcher, array $attributes): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new FindOrCreateResourceEvent(TaxonFactoryInterface::class, $attributes),
        );

        return $event->getResource();
    }
}
