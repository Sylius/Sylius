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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantOptionsMapProvider;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class ProductVariantOptionsMapProviderTest extends TestCase
{
    private MockObject&ProductVariantInterface $variant;

    private MockObject&ProductOptionValueInterface $optionValue;

    private ProductVariantOptionsMapProvider $provider;

    protected function setUp(): void
    {
        $this->variant = $this->createMock(ProductVariantInterface::class);
        $this->optionValue = $this->createMock(ProductOptionValueInterface::class);
        $this->provider = new ProductVariantOptionsMapProvider();
    }

    public function testShouldImplementProductVariantOptionsMapDataProviderInterface(): void
    {
        $this->assertInstanceOf(ProductVariantMapProviderInterface::class, $this->provider);
    }

    public function testShouldNotSupportVariantsWithNoOptionValues(): void
    {
        $this->variant->expects($this->once())->method('getOptionValues')->willReturn(new ArrayCollection());

        $this->assertFalse($this->provider->supports($this->variant, []));
    }

    public function testShouldSupportVariantsWithOptionValue(): void
    {
        $this->variant->expects($this->once())->method('getOptionValues')->willReturn(new ArrayCollection([
            $this->optionValue,
        ]));

        $this->assertTrue($this->provider->supports($this->variant, []));
    }

    public function testShouldProvideMapOfVariantOptions(): void
    {
        $secondOptionValue = $this->createMock(ProductOptionValueInterface::class);
        $this->optionValue->expects($this->once())->method('getOptionCode')->willReturn('first_option');
        $this->optionValue->expects($this->once())->method('getCode')->willReturn('first_option_value');
        $secondOptionValue->expects($this->once())->method('getOptionCode')->willReturn('second_option');
        $secondOptionValue->expects($this->once())->method('getCode')->willReturn('second_option_value');
        $this->variant->expects($this->once())->method('getOptionValues')->willReturn(new ArrayCollection([
            $this->optionValue,
            $secondOptionValue,
        ]));

        $this->assertEquals(
            [
                'first_option' => 'first_option_value',
                'second_option' => 'second_option_value',
            ],
            $this->provider->provide($this->variant, []),
        );
    }
}
