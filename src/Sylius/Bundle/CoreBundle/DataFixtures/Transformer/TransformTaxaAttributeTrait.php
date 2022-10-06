<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateTaxonByQueryStringEvent;

trait TransformTaxaAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformTaxaAttribute(array $attributes): array
    {
        $taxa = [];
        foreach ($attributes['taxons'] ?? $attributes['taxa'] as $taxon) {
            if (\is_string($taxon)) {
                $event = new FindOrCreateTaxonByQueryStringEvent($taxon);
                $this->eventDispatcher->dispatch($event);

                $taxon = $event->getTaxon();
            }
            $taxa[] = $taxon;
        }
        $attributes['taxa'] = $taxa;

        return $attributes;
    }
}
