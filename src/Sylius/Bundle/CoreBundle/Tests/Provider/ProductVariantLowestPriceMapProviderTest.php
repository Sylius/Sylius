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

namespace Sylius\Bundle\CoreBundle\Tests\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantLowestPriceMapProvider;

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. */
final class ProductVariantLowestPriceMapProviderTest extends TestCase
{
    private ProductVariantPricesCalculatorInterface&MockObject $calculator;

    protected function setUp(): void
    {
        $this->calculator = $this->createMock(ProductVariantPricesCalculatorInterface::class);
    }

    public function testDoesNotSupportVariantsWithNoLowestPriceInChannel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $variant->method('getChannelPricingForChannel')->willReturn($channelPricing);

        $provider = new ProductVariantLowestPriceMapProvider($this->calculator);

        $this->assertFalse($provider->supports($variant, ['channel' => $channel]));
    }
}
