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

namespace Sylius\Bundle\CoreBundle\Tests\Helper;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Bundle\CoreBundle\Templating\Helper\PriceHelper;

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. */
final class PriceHelperTest extends TestCase
{
    private ProductVariantPricesCalculatorInterface&MockObject $productVariantPricesCalculator;

    protected function setUp(): void
    {
        $this->productVariantPricesCalculator = $this->createMock(ProductVariantPricesCalculatorInterface::class);
    }

    public function testReturnsRegularPriceIfCalculateLowestPriceBeforeDiscountIsNotPresentOnObject(): void
    {
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->productVariantPricesCalculator
            ->method('calculate')
            ->with($productVariant, ['channel' => $channel])
            ->willReturn(1000)
        ;

        $helper = new PriceHelper($this->productVariantPricesCalculator);

        $this->assertEquals(1000, $helper->getLowestPriceBeforeDiscount($productVariant, ['channel' => $channel]));
    }
}
