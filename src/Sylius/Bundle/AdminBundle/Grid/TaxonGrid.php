<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Grid;

use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: '%sylius.model.taxon.class%', name: self::NAME)]
final class TaxonGrid implements TaxonGridInterface
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createListQueryBuilder')
            ->setLimits([10, 25, 50])

            ->withFilters(
                StringFilter::create('code', ['code']),
                StringFilter::create('name', ['translation.name']),
            )
        ;
    }
}
