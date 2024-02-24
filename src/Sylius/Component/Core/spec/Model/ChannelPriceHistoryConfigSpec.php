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

namespace spec\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\TaxonInterface;

final class ChannelPriceHistoryConfigSpec extends ObjectBehavior
{
    function its_default_lowest_price_for_discounted_products_checking_period_is_30(): void
    {
        $this->getLowestPriceForDiscountedProductsCheckingPeriod()->shouldReturn(30);
    }

    function its_lowest_price_for_discounted_products_checking_period_is_mutable(): void
    {
        $this->setLowestPriceForDiscountedProductsCheckingPeriod(60);
        $this->getLowestPriceForDiscountedProductsCheckingPeriod()->shouldReturn(60);
    }

    function its_default_lowest_price_for_discounted_products_visible_is_true(): void
    {
        $this->isLowestPriceForDiscountedProductsVisible()->shouldReturn(true);
    }

    function its_lowest_price_for_discounted_products_visible_is_mutable(): void
    {
        $this->setLowestPriceForDiscountedProductsVisible(false);
        $this->isLowestPriceForDiscountedProductsVisible()->shouldReturn(false);
    }

    function it_adds_taxon_excluded_from_showing_lowest_price(TaxonInterface $taxon): void
    {
        $this->addTaxonExcludedFromShowingLowestPrice($taxon);

        $this->hasTaxonExcludedFromShowingLowestPrice($taxon)->shouldReturn(true);
        $this->getTaxonsExcludedFromShowingLowestPrice()->shouldBeLike(new ArrayCollection([
            $taxon->getWrappedObject(),
        ]));
    }

    function it_removes_taxon_excluded_from_showing_lowest_price(
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        TaxonInterface $thirdTaxon,
    ): void {
        $this->addTaxonExcludedFromShowingLowestPrice($firstTaxon);
        $this->addTaxonExcludedFromShowingLowestPrice($secondTaxon);
        $this->addTaxonExcludedFromShowingLowestPrice($thirdTaxon);

        $this->removeTaxonExcludedFromShowingLowestPrice($secondTaxon);

        $this->hasTaxonExcludedFromShowingLowestPrice($secondTaxon)->shouldReturn(false);
    }

    function it_clears_taxons_excluded_from_showing_lowest_price(
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        TaxonInterface $thirdTaxon,
    ): void {
        $this->addTaxonExcludedFromShowingLowestPrice($firstTaxon);
        $this->addTaxonExcludedFromShowingLowestPrice($secondTaxon);
        $this->addTaxonExcludedFromShowingLowestPrice($thirdTaxon);

        $this->clearTaxonsExcludedFromShowingLowestPrice();

        $this->getTaxonsExcludedFromShowingLowestPrice()->shouldBeLike(new ArrayCollection());
    }
}
