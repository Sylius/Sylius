<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactoryInterface;

trait TransformTaxaAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformTaxaAttribute(array $attributes): array
    {
        $taxa = [];
        foreach ($attributes['taxons'] ?? $attributes['taxa'] as $taxon) {
            if (\is_string($taxon)) {
                /** @var FindOrCreateResourceEvent $event */
                $event = $this->eventDispatcher->dispatch(
                    new FindOrCreateResourceEvent(TaxonFactoryInterface::class, ['code' => $taxon])
                );

                $taxon = $event->getResource();
            }
            $taxa[] = $taxon;
        }
        $attributes['taxa'] = $taxa;

        return $attributes;
    }
}
