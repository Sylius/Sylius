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

namespace Tests\Sylius\Component\Core\Calculator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculator;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantPriceCalculatorTest extends TestCase
{
    private MockObject&ProductVariantLowestPriceDisplayCheckerInterface $productVariantLowestPriceDisplayChecker;

    private ChannelInterface&MockObject  $channel;

    private ChannelPricingInterface &MockObject $channelPricing;

    private MockObject&ProductVariantInterface $productVariant;

    private ProductVariantPriceCalculator $calculator;

    protected function setUp(): void
    {
        $this->productVariantLowestPriceDisplayChecker = $this->createMock(ProductVariantLowestPriceDisplayCheckerInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->channelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->calculator = new ProductVariantPriceCalculator($this->productVariantLowestPriceDisplayChecker);
    }

    public function testShouldImplementProductVariantPriceCalculatorInterface(): void
    {
        $this->assertInstanceOf(ProductVariantPricesCalculatorInterface::class, $this->calculator);
    }

    public function testShouldGetPriceForProductVariantInGivenChannel(): void
    {
        $this->productVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->channelPricing);
        $this->channelPricing->expects($this->exactly(2))->method('getPrice')->willReturn(1000);

        $this->assertSame(1000, $this->calculator->calculate($this->productVariant, ['channel' => $this->channel]));
    }

    public function testShouldThrowChannelNotDefinedExceptionIfThereIsNoChannelPricing(): void
    {
        $this->expectException(MissingChannelConfigurationException::class);
        $this->channel->expects($this->once())->method('getName')->willReturn('WEB');
        $this->productVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn(null);
        $this->productVariant->expects($this->once())->method('getDescriptor')->willReturn('Red variant (RED_VARIANT)');

        $this->calculator->calculate($this->productVariant, ['channel' => $this->channel]);
    }

    public function testShouldThrowChannelNotDefinedExceptionIfThereIsNoVariantPriceForGivenChannel(): void
    {
        $this->expectException(MissingChannelConfigurationException::class);
        $this->channel->expects($this->once())->method('getName')->willReturn('WEB');
        $this->productVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->channelPricing);
        $this->channelPricing->expects($this->once())->method('getPrice')->willReturn(null);
        $this->productVariant->expects($this->once())->method('getDescriptor')->willReturn('Red variant (RED_VARIANT)');

        $this->calculator->calculate($this->productVariant, ['channel' => $this->channel]);
    }

    public function testShouldThrowExceptionIfNoChannelIsDefinedInConfiguration(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->calculator->calculate($this->productVariant, []);
    }

    public function testShouldGetOriginalPriceForProductVariantInGivenChannel(): void
    {
        $this->productVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->channelPricing);
        $this->channelPricing->expects($this->exactly(2))->method('getOriginalPrice')->willReturn(1000);

        $this->assertSame(1000, $this->calculator->calculateOriginal($this->productVariant, ['channel' => $this->channel]));
    }

    public function testShouldGetPriceForProductVariantIfItHasNoOriginalPriceInGivenChannel(): void
    {
        $this->productVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->channelPricing);
        $this->channelPricing->expects($this->exactly(2))->method('getPrice')->willReturn(1000);
        $this->channelPricing->expects($this->once())->method('getOriginalPrice')->willReturn(null);

        $this->assertSame(1000, $this->calculator->calculateOriginal($this->productVariant, ['channel' => $this->channel]));
    }

    public function testShouldThrowChannelNotDefinedExceptionIfThereIsNoChannelPricingWhenCalculatingOriginalPrice(): void
    {
        $this->expectException(MissingChannelConfigurationException::class);
        $this->channel->expects($this->once())->method('getName')->willReturn('WEB');
        $this->productVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn(null);
        $this->productVariant->expects($this->once())->method('getDescriptor')->willReturn('Red variant (RED_VARIANT)');

        $this->calculator->calculateOriginal($this->productVariant, ['channel' => $this->channel]);
    }

    public function testShouldThrowChannelNotDefinedExceptionIfThereIsNoVariantPriceForGivenChannelWhenCalculatingOriginalPrice(): void
    {
        $this->expectException(MissingChannelConfigurationException::class);
        $this->channel->expects($this->once())->method('getName')->willReturn('WEB');
        $this->channel->expects($this->once())->method('getName')->willReturn('WEB');
        $this->productVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->channelPricing);
        $this->channelPricing->expects($this->once())->method('getOriginalPrice')->willReturn(null);
        $this->channelPricing->expects($this->once())->method('getPrice')->willReturn(null);
        $this->productVariant->expects($this->once())->method('getDescriptor')->willReturn('Red variant (RED_VARIANT)');

        $this->calculator->calculateOriginal($this->productVariant, ['channel' => $this->channel]);
    }

    public function testShouldThrowExceptionIfNoChannelIsDefinedInConfigurationWhenCalculatingOriginalPrice(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->calculator->calculateOriginal($this->productVariant, []);
    }

    public function testShouldReturnLowestPriceBeforeDiscount(): void
    {
        $this->productVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->channelPricing);
        $this->productVariantLowestPriceDisplayChecker
            ->expects($this->once())
            ->method('isLowestPriceDisplayable')
            ->with($this->productVariant, ['channel' => $this->channel])
            ->willReturn(true);
        $this->channelPricing->expects($this->once())->method('getLowestPriceBeforeDiscount')->willReturn(2100);

        $this->assertSame(
            2100,
            $this->calculator->calculateLowestPriceBeforeDiscount($this->productVariant, ['channel' => $this->channel]),
        );
    }

    public function testShouldReturnNullWhenShowingLowestPriceBeforeDiscountShouldNotBeDisplayed(): void
    {
        $this->productVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->channelPricing);
        $this->productVariantLowestPriceDisplayChecker
            ->expects($this->once())
            ->method('isLowestPriceDisplayable')
            ->with($this->productVariant, ['channel' => $this->channel])
            ->willReturn(false);
        $this->channelPricing->expects($this->never())->method('getLowestPriceBeforeDiscount');

        $this->assertNull(
            $this->calculator->calculateLowestPriceBeforeDiscount($this->productVariant, ['channel' => $this->channel]),
        );
    }

    public function testShouldThrowChannelNotDefinedExceptionIfThereIsNoChannelPricingWhenProvidingLowestPriceBeforeDiscount(): void
    {
        $this->expectException(MissingChannelConfigurationException::class);
        $this->productVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn(null);
        $this->productVariant->expects($this->once())->method('getDescriptor')->willReturn('Red variant (RED_VARIANT)');
        $this->channel->expects($this->once())->method('getName');

        $this->calculator->calculateLowestPriceBeforeDiscount($this->productVariant, ['channel' => $this->channel]);
    }

    public function testShouldThrowExceptionIfThereIsNoChannelPassedInContext(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->calculator->calculateLowestPriceBeforeDiscount($this->productVariant, []);
    }

    public function testShouldThrowExceptionIfThereIsNoChannelSetUnderTheChannelKeyInContext(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->calculator->calculateLowestPriceBeforeDiscount($this->productVariant, ['channel' => new \stdClass()]);
    }
}
