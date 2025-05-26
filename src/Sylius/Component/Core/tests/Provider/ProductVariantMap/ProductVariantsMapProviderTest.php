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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProvider;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProviderInterface;

final class ProductVariantsMapProviderTest extends TestCase
{
    private MockObject&ProductVariantMapProviderInterface $firstProvider;

    private MockObject&ProductVariantMapProviderInterface $secondProvider;

    private MockObject&ProductVariantMapProviderInterface $thirdProvider;

    private ProductVariantsMapProvider $provider;

    protected function setUp(): void
    {
        $this->firstProvider = $this->createMock(ProductVariantMapProviderInterface::class);
        $this->secondProvider = $this->createMock(ProductVariantMapProviderInterface::class);
        $this->thirdProvider = $this->createMock(ProductVariantMapProviderInterface::class);
        $this->provider = new ProductVariantsMapProvider([
            $this->firstProvider,
            $this->secondProvider,
            $this->thirdProvider,
        ]);
    }

    public function testShouldImplementProductVariantsMapProviderInterface(): void
    {
        $this->assertInstanceOf(ProductVariantsMapProviderInterface::class, $this->provider);
    }

    public function testShouldProvidesDataForAllProductsEnabledVariants(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $firstVariant = $this->createMock(ProductVariantInterface::class);
        $secondVariant = $this->createMock(ProductVariantInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $product->expects($this->once())->method('getEnabledVariants')->willReturn(new ArrayCollection([
            $firstVariant,
            $secondVariant,
        ]));

        $this->firstProvider
            ->expects($this->exactly(2))
            ->method('supports')
            ->willReturnMap([
                [$firstVariant, ['channel' => $channel], true],
                [$secondVariant, ['channel' => $channel], false],
            ]);
        $this->firstProvider
            ->expects($this->once())
            ->method('provide')
            ->with($firstVariant, ['channel' => $channel])
            ->willReturn(['first-first' => ['some']]);

        $this->secondProvider
            ->expects($this->exactly(2))
            ->method('supports')
            ->willReturnMap([
                [$firstVariant, ['channel' => $channel], false],
                [$secondVariant, ['channel' => $channel], true],
            ]);
        $this->secondProvider
            ->expects($this->once())
            ->method('provide')
            ->with($secondVariant, ['channel' => $channel])
            ->willReturn(['second-second' => ['more']]);

        $this->thirdProvider
            ->expects($this->exactly(2))
            ->method('supports')
            ->willReturnMap([
                [$firstVariant, ['channel' => $channel], true],
                [$secondVariant, ['channel' => $channel], true],
            ]);

        $thirdProviderProvideInvokedCount = $this->exactly(2);
        $this->thirdProvider
            ->expects($thirdProviderProvideInvokedCount)
            ->method('provide')
            ->willReturnCallback(function ($variant) use ($thirdProviderProvideInvokedCount, $firstVariant, $secondVariant) {
                if ($thirdProviderProvideInvokedCount->numberOfInvocations() === 1) {
                    $this->assertSame($firstVariant, $variant);

                    return ['first-third' => ['data']];
                }
                if ($thirdProviderProvideInvokedCount->numberOfInvocations() === 2) {
                    $this->assertSame($secondVariant, $variant);

                    return ['second-third' => ['data']];
                }
            });

        $this->assertEquals(
            [
                [
                    'first-first' => ['some'],
                    'first-third' => ['data'],
                ],
                [
                    'second-second' => ['more'],
                    'second-third' => ['data'],
                ],
            ],
            $this->provider->provide($product, ['channel' => $channel]),
        );
    }
}
