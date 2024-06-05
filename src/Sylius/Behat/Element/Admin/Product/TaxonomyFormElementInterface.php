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

namespace Sylius\Behat\Element\Admin\Product;

use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface TaxonomyFormElementInterface
{
    public function selectMainTaxon(TaxonInterface $taxon): void;

    public function hasMainTaxonWithName(string $taxonName): bool;

    public function checkProductTaxon(TaxonInterface $taxon): void;

    public function selectProductTaxon(TaxonInterface $taxon): void;

    public function unselectProductTaxon(TaxonInterface $taxon): void;

    public function hasMainTaxon(): bool;

    public function isTaxonVisibleInMainTaxonList(string $taxonName): bool;

    public function isTaxonChosen(string $taxonName): bool;
}
