<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxCategoryFactoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Zenstruck\Foundry\Proxy;

trait FindOrCreateTaxCategoryTrait
{
    /**
     * @return TaxCategoryInterface|Proxy
     */
    private function findOrCreateTaxCategory(EventDispatcherInterface $eventDispatcher, array $attributes): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new FindOrCreateResourceEvent(TaxCategoryFactoryInterface::class, $attributes),
        );

        return $event->getResource();
    }
}
