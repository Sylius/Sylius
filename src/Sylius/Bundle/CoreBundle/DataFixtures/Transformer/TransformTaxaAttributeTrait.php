<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateTaxonTrait;

trait TransformTaxaAttributeTrait
{
    use FindOrCreateTaxonTrait;

    private function transformTaxaAttribute(EventDispatcherInterface $eventDispatcher, array $attributes): array
    {
        $taxa = [];
        foreach ($attributes['taxons'] ?? $attributes['taxa'] as $taxon) {
            if (\is_string($taxon)) {
                $taxon = $this->findOrCreateTaxon($eventDispatcher, ['code' => $taxon]);
            }
            $taxa[] = $taxon;
        }
        $attributes['taxa'] = $taxa;

        return $attributes;
    }
}
