<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Grid;

use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: self::NAME)]
final class TaxonGrid implements TaxonGridInterface
{
    public function __construct(
        private readonly string $taxonClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->taxonClass)
            ->setRepositoryMethod('createListQueryBuilder')
            ->setLimits([10, 25, 50])

            // -- Fields
            ->addFilter(StringFilter::create('code', ['code']))
            ->addFilter(StringFilter::create('name', ['translation.name']))
        ;
    }
}
