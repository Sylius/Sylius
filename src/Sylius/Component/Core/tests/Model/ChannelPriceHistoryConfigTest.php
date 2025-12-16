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

namespace Tests\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfig;
use Sylius\Component\Core\Model\TaxonInterface;

final class ChannelPriceHistoryConfigTest extends TestCase
{
    private MockObject&TaxonInterface $firstTaxon;

    private MockObject&TaxonInterface $secondTaxon;

    private MockObject&TaxonInterface $thirdTaxon;

    private ChannelPriceHistoryConfig $channelPriceHistoryConfig;

    protected function setUp(): void
    {
        $this->firstTaxon = $this->createMock(TaxonInterface::class);
        $this->secondTaxon = $this->createMock(TaxonInterface::class);
        $this->thirdTaxon = $this->createMock(TaxonInterface::class);
        $this->channelPriceHistoryConfig = new ChannelPriceHistoryConfig();
    }

    public function testShouldDefaultLowestPriceForDiscountedProductsCheckingPeriodBeThirtyByDefault(): void
    {
        $this->assertSame(30, $this->channelPriceHistoryConfig->getLowestPriceForDiscountedProductsCheckingPeriod());
    }

    public function testShouldLowestPriceForDiscountedProductsCheckingPeriodBeMutable(): void
    {
        $this->channelPriceHistoryConfig->setLowestPriceForDiscountedProductsCheckingPeriod(60);

        $this->assertSame(60, $this->channelPriceHistoryConfig->getLowestPriceForDiscountedProductsCheckingPeriod());
    }

    public function testShouldDefaultLowestPriceForDiscountedProductsVisibleBeTrueByDefault(): void
    {
        $this->assertTrue($this->channelPriceHistoryConfig->isLowestPriceForDiscountedProductsVisible());
    }

    public function testShouldLowestPriceForDiscountedProductsVisibleBeMutable(): void
    {
        $this->channelPriceHistoryConfig->setLowestPriceForDiscountedProductsVisible(false);

        $this->assertFalse($this->channelPriceHistoryConfig->isLowestPriceForDiscountedProductsVisible());
    }

    public function testShouldAddTaxonExcludedFromShowingLowestPrice(): void
    {
        $this->channelPriceHistoryConfig->addTaxonExcludedFromShowingLowestPrice($this->firstTaxon);

        $this->assertTrue($this->channelPriceHistoryConfig->hasTaxonExcludedFromShowingLowestPrice($this->firstTaxon));
        $this->assertEquals(
            new ArrayCollection([$this->firstTaxon]),
            $this->channelPriceHistoryConfig->getTaxonsExcludedFromShowingLowestPrice(),
        );
    }

    public function testShouldRemoveTaxonExcludedFromShowingLowestPrice(): void
    {
        $this->channelPriceHistoryConfig->addTaxonExcludedFromShowingLowestPrice($this->firstTaxon);
        $this->channelPriceHistoryConfig->addTaxonExcludedFromShowingLowestPrice($this->secondTaxon);
        $this->channelPriceHistoryConfig->addTaxonExcludedFromShowingLowestPrice($this->thirdTaxon);

        $this->channelPriceHistoryConfig->removeTaxonExcludedFromShowingLowestPrice($this->secondTaxon);

        $this->assertFalse($this->channelPriceHistoryConfig->hasTaxonExcludedFromShowingLowestPrice($this->secondTaxon));
    }

    public function testShouldClearTaxonsExcludedFromShowingLowestPrice(): void
    {
        $this->channelPriceHistoryConfig->addTaxonExcludedFromShowingLowestPrice($this->firstTaxon);
        $this->channelPriceHistoryConfig->addTaxonExcludedFromShowingLowestPrice($this->secondTaxon);
        $this->channelPriceHistoryConfig->addTaxonExcludedFromShowingLowestPrice($this->thirdTaxon);

        $this->channelPriceHistoryConfig->clearTaxonsExcludedFromShowingLowestPrice();

        $this->assertEquals(
            new ArrayCollection(),
            $this->channelPriceHistoryConfig->getTaxonsExcludedFromShowingLowestPrice(),
        );
    }
}
