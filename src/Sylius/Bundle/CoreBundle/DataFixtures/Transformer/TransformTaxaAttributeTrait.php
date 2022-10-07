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
                $event = new FindOrCreateResourceEvent(TaxonFactoryInterface::class, ['code' => $taxon]);
                $this->eventDispatcher->dispatch($event);

                $taxon = $event->getResource();
            }
            $taxa[] = $taxon;
        }
        $attributes['taxa'] = $taxa;

        return $attributes;
    }
}
