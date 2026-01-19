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

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Model\ProductOption;
use Sylius\Component\Product\Model\ProductOptionValue;

final class ProductOptionTest extends TestCase
{
    private ProductOption $productOption;

    private MockObject&ProductOptionValue $productOptionValue;

    protected function setUp(): void
    {
        $this->productOption = new ProductOption();
        $this->productOption->setCurrentLocale('en_US');
        $this->productOption->setFallbackLocale('en_US');
        $this->productOptionValue = $this->createMock(ProductOptionValue::class);
    }

    public function testHasNoIdByDefault(): void
    {
        $this->assertNull($this->productOption->getId());
    }

    public function testHasNoCodeByDefault(): void
    {
        $this->assertNull($this->productOption->getCode());
    }

    public function testItsCodeIsMutable(): void
    {
        $this->productOption->setCode('color');
        $this->assertSame('color', $this->productOption->getCode());
    }

    public function testHasNoNameByDefault(): void
    {
        $this->assertNull($this->productOption->getName());
    }

    public function testItsNameIsMutable(): void
    {
        $this->productOption->setName('Color');
        $this->assertSame('Color', $this->productOption->getName());
    }

    public function testHasNoPositionByDefault(): void
    {
        $this->assertNull($this->productOption->getPosition());
    }

    public function testItsPositionIsMutable(): void
    {
        $this->productOption->setPosition(10);
        $this->assertSame(10, $this->productOption->getPosition());
    }

    public function testHasAnEmptyCollectionOfValuesByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->productOption->getValues());
        $this->assertSame(0, $this->productOption->getValues()->count());
    }

    public function testCanHaveAValueAdded(): void
    {
        $this->productOption->addValue($this->productOptionValue);
        $this->assertTrue($this->productOption->hasValue($this->productOptionValue));
    }

    public function testCanHaveALocaleRemoved(): void
    {
        $this->productOption->addValue($this->productOptionValue);
        $this->productOption->removeValue($this->productOptionValue);
        $this->assertFalse($this->productOption->hasValue($this->productOptionValue));
    }
}
