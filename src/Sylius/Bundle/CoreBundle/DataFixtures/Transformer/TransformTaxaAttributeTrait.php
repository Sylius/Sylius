<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactoryInterface;

trait TransformTaxaAttributeTrait
{
    private TaxonFactoryInterface $taxonFactory;

    private function transformTaxaAttribute(array $attributes): array
    {
        $taxa = [];
        foreach ($attributes['taxons'] ?? $attributes['taxa']  as $taxon) {
            if (\is_string($taxon)) {
                $taxon = $this->taxonFactory::findOrCreate(['code' => $taxon]);
            }
            $taxa[] = $taxon;
        }
        $attributes['taxa'] = $taxa;

        return $attributes;
    }
}
