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

namespace Tests\Sylius\Component\Product\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariant;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\ToggleableInterface;

final class ProductVariantTest extends TestCase
{
    private ProductVariant $productVariant;

    private MockObject&ProductOptionValueInterface $optionValue;

    protected function setUp(): void
    {
        $this->productVariant = new ProductVariant();
        $this->optionValue = $this->createMock(ProductOptionValueInterface::class);
    }

    public function testImplementsSyliusProductVariantInterface(): void
    {
        $this->assertInstanceOf(ProductVariantInterface::class, $this->productVariant);
    }

    public function testImplementsToggleableInterface(): void
    {
        $this->assertInstanceOf(ToggleableInterface::class, $this->productVariant);
    }

    public function testImplementsSyliusResourceInterface(): void
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->productVariant);
    }

    public function testAddsAnOptionValue(): void
    {
        $this->productVariant->addOptionValue($this->optionValue);
        $this->assertTrue($this->productVariant->hasOptionValue($this->optionValue));
    }

    public function testRemovesAnOptionValue(): void
    {
        $this->productVariant->addOptionValue($this->optionValue);
        $this->productVariant->removeOptionValue($this->optionValue);
        $this->assertFalse($this->productVariant->hasOptionValue($this->optionValue));
    }

    public function testHasNoPositionByDefault(): void
    {
        $this->assertNull($this->productVariant->getPosition());
    }

    public function testItsPositionIsMutable(): void
    {
        $this->productVariant->setPosition(10);
        $this->assertSame(10, $this->productVariant->getPosition());
    }

    public function testEnabledByDefault(): void
    {
        self::assertTrue($this->productVariant->isEnabled());
    }

    public function testToggleable(): void
    {
        $this->productVariant->disable();
        self::assertFalse($this->productVariant->isEnabled());

        $this->productVariant->enable();
        self::assertTrue($this->productVariant->isEnabled());
    }
}
