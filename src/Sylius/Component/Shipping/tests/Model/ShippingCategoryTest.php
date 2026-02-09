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

namespace Tests\Sylius\Component\Shipping\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Shipping\Model\ShippingCategory;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

final class ShippingCategoryTest extends TestCase
{
    private ShippingCategory $shippingCategory;

    protected function setUp(): void
    {
        $this->shippingCategory = new ShippingCategory();
    }

    public function testShouldImplementShippingCategoryInterface(): void
    {
        $this->assertInstanceOf(ShippingCategoryInterface::class, $this->shippingCategory);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->shippingCategory->getId());
    }

    public function testShouldCodeBeMutable(): void
    {
        $this->shippingCategory->setCode('SC2');

        $this->assertSame('SC2', $this->shippingCategory->getCode());
    }

    public function testShouldBeUnnamedByDefault(): void
    {
        $this->assertNull($this->shippingCategory->getName());
    }

    public function testShouldNameBeMutable(): void
    {
        $this->shippingCategory->setName('Shippingable goods');

        $this->assertSame('Shippingable goods', $this->shippingCategory->getName());
    }

    public function testShouldNotHaveDescriptionByDefault(): void
    {
        $this->assertNull($this->shippingCategory->getDescription());
    }

    public function testShouldDescriptionBeMutable(): void
    {
        $this->shippingCategory->setDescription('All shippingable goods');

        $this->assertSame('All shippingable goods', $this->shippingCategory->getDescription());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->shippingCategory->getCreatedAt());
    }

    public function testShouldCreationDateBeMutable(): void
    {
        $date = new \DateTime();

        $this->shippingCategory->setCreatedAt($date);

        $this->assertSame($date, $this->shippingCategory->getCreatedAt());
    }

    public function testShouldNotHaveLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->shippingCategory->getUpdatedAt());
    }

    public function testShouldLastUpdateDateBeMutable(): void
    {
        $date = new \DateTime();

        $this->shippingCategory->setUpdatedAt($date);

        $this->assertSame($date, $this->shippingCategory->getUpdatedAt());
    }
}
