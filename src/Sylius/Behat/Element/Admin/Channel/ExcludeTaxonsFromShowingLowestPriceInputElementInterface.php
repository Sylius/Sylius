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

namespace Sylius\Behat\Element\Admin\Channel;

use Sylius\Component\Core\Model\TaxonInterface;

interface ExcludeTaxonsFromShowingLowestPriceInputElementInterface
{
    public function excludeTaxon(TaxonInterface $taxon): void;

    public function removeExcludedTaxon(TaxonInterface $taxon): void;

    public function hasTaxonExcluded(TaxonInterface $taxons): bool;
}
