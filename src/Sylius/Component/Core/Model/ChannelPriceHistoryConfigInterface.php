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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Resource\Model\ResourceInterface;

interface ChannelPriceHistoryConfigInterface extends ResourceInterface
{
    public function isLowestPriceForDiscountedProductsVisible(): bool;

    public function setLowestPriceForDiscountedProductsVisible(bool $visible = true): void;

    public function getLowestPriceForDiscountedProductsCheckingPeriod(): int;

    public function setLowestPriceForDiscountedProductsCheckingPeriod(int $periodInDays): void;

    public function getTaxonsExcludedFromShowingLowestPrice(): Collection;

    public function hasTaxonExcludedFromShowingLowestPrice(TaxonInterface $taxon): bool;

    public function addTaxonExcludedFromShowingLowestPrice(TaxonInterface $taxon): void;

    public function removeTaxonExcludedFromShowingLowestPrice(TaxonInterface $taxon): void;

    public function clearTaxonsExcludedFromShowingLowestPrice(): void;
}
