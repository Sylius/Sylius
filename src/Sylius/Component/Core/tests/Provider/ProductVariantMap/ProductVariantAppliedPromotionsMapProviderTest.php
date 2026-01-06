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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantAppliedPromotionsMapProvider;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface;

final class ProductVariantAppliedPromotionsMapProviderTest extends TestCase
{
    private MockObject&ProductVariantInterface $variant;

    private ChannelInterface&MockObject $channel;

    private MockObject&PromotionInterface $promotion;

    private ProductVariantAppliedPromotionsMapProvider $provider;

    protected function setUp(): void
    {
        $this->variant = $this->createMock(ProductVariantInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->provider = new ProductVariantAppliedPromotionsMapProvider();
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

    public function testShouldNotSupportVariantsWithNoPromotionsApplied(): void
    {
        $this->variant
            ->expects($this->once())
            ->method('getAppliedPromotionsForChannel')
            ->with($this->channel)
            ->willReturn(new ArrayCollection());

        $this->assertFalse($this->provider->supports($this->variant, ['channel' => $this->channel]));
    }

    public function testShouldSupportVariantsWithAppliedPromotions(): void
    {
        $this->variant
            ->expects($this->once())
            ->method('getAppliedPromotionsForChannel')
            ->with($this->channel)
            ->willReturn(new ArrayCollection([$this->promotion]));

        $this->assertTrue($this->provider->supports($this->variant, ['channel' => $this->channel]));
    }

    public function testShouldProvideMapOfVariantAppliedPromotions(): void
    {
        $secondPromotion = $this->createMock(PromotionInterface::class);
        $this->variant
            ->expects($this->once())
            ->method('getAppliedPromotionsForChannel')
            ->with($this->channel)
            ->willReturn(new ArrayCollection([
                $this->promotion,
                $secondPromotion,
            ]));

        $this->assertEquals(
            ['applied_promotions' => [$this->promotion, $secondPromotion]],
            $this->provider->provide($this->variant, ['channel' => $this->channel]),
        );
    }
}
