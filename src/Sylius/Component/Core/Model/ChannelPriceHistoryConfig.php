<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

class ChannelPriceHistoryConfig implements ChannelPriceHistoryConfigInterface
{
    /** @var mixed */
    protected $id;

    protected int $lowestPriceForDiscountedProductsCheckingPeriod = 30;

    protected bool $lowestPriceForDiscountedProductsVisible = true;

    /**
     * @var Collection|TaxonInterface[]
     *
     * @psalm-var Collection<array-key, TaxonInterface>
     */
    protected Collection $taxonsExcludedFromShowingLowestPrice;

    public function __construct()
    {
        $this->taxonsExcludedFromShowingLowestPrice = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLowestPriceForDiscountedProductsCheckingPeriod(): int
    {
        return $this->lowestPriceForDiscountedProductsCheckingPeriod;
    }

    public function setLowestPriceForDiscountedProductsCheckingPeriod(int $periodInDays): void
    {
        $this->lowestPriceForDiscountedProductsCheckingPeriod = $periodInDays;
    }

    public function isLowestPriceForDiscountedProductsVisible(): bool
    {
        return $this->lowestPriceForDiscountedProductsVisible;
    }

    public function setLowestPriceForDiscountedProductsVisible(bool $visible = true): void
    {
        $this->lowestPriceForDiscountedProductsVisible = $visible;
    }

    /** @return Collection<array-key, TaxonInterface> */
    public function getTaxonsExcludedFromShowingLowestPrice(): Collection
    {
        return $this->taxonsExcludedFromShowingLowestPrice;
    }

    public function hasTaxonExcludedFromShowingLowestPrice(TaxonInterface $taxon): bool
    {
        return $this->taxonsExcludedFromShowingLowestPrice->contains($taxon);
    }

    public function addTaxonExcludedFromShowingLowestPrice(TaxonInterface $taxon): void
    {
        if (!$this->hasTaxonExcludedFromShowingLowestPrice($taxon)) {
            $this->taxonsExcludedFromShowingLowestPrice->add($taxon);
        }
    }

    public function removeTaxonExcludedFromShowingLowestPrice(TaxonInterface $taxon): void
    {
        if ($this->hasTaxonExcludedFromShowingLowestPrice($taxon)) {
            $this->taxonsExcludedFromShowingLowestPrice->removeElement($taxon);
        }
    }

    public function clearTaxonsExcludedFromShowingLowestPrice(): void
    {
        $this->taxonsExcludedFromShowingLowestPrice->clear();
    }
}
