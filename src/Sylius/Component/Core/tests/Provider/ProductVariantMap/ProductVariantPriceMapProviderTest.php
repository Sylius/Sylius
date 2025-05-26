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

namespace Tests\Sylius\Component\Core\Provider\ProductVariantMap;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantPriceMapProvider;

final class ProductVariantPriceMapProviderTest extends TestCase
{
    private MockObject&ProductVariantInterface $variant;

    private ChannelInterface&MockObject $channel;

    private ChannelPricingInterface&MockObject $channelPricing;

    private MockObject&ProductVariantPricesCalculatorInterface $calculator;

    private ProductVariantPriceMapProvider $provider;

    protected function setUp(): void
    {
        $this->calculator = $this->createMock(ProductVariantPricesCalculatorInterface::class);
        $this->variant = $this->createMock(ProductVariantInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->channelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->provider = new ProductVariantPriceMapProvider($this->calculator);
    }

    public function testShouldImplementProductVariantOptionsMapDataProviderInterface(): void
    {
        $this->assertInstanceOf(ProductVariantMapProviderInterface::class, $this->provider);
    }

    public function testShouldNotSupportContextWithNoChannel(): void
    {
        $this->assertFalse($this->provider->supports($this->variant, []));
    }

    public function testShouldNotSupportContextWithChannelThatIsNotChannelInterface(): void
    {
        $this->assertFalse($this->provider->supports($this->variant, ['channel' => 'not_a_channel']));
    }

    public function testShouldNotSupportVariantsWithNoChannelPricingInChannel(): void
    {
        $this->variant->expects($this->once())->method('getChannelPricingForChannel')->with($this->channel)->willReturn(null);

        $this->assertFalse($this->provider->supports($this->variant, ['channel' => $this->channel]));
    }

    public function testShouldSupportVariantsWithChannelPricingInChannel(): void
    {
        $this->variant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->channelPricing);

        $this->assertTrue($this->provider->supports($this->variant, ['channel' => $this->channel]));
    }

    public function testShouldProvidePriceOfVariantInChannel(): void
    {
        $this->calculator
            ->expects($this->once())
            ->method('calculate')
            ->with($this->variant, ['channel' => $this->channel])
            ->willReturn(1000);

        $this->assertEquals(['value' => 1000], $this->provider->provide($this->variant, ['channel' => $this->channel]));
    }
}
