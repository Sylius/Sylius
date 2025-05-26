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

namespace Tests\Sylius\Component\Taxation\Model;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Taxation\Model\TaxCategory;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

final class TaxCategoryTest extends TestCase
{
    private TaxCategory $taxCategory;

    protected function setUp(): void
    {
        $this->taxCategory = new TaxCategory();
    }

    public function testShouldImplementTaxCategoryInterface(): void
    {
        $this->assertInstanceOf(TaxCategoryInterface::class, $this->taxCategory);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->taxCategory->getId());
    }

    public function testShouldCodeBeMutable(): void
    {
        $this->taxCategory->setCode('TC1');

        $this->assertSame('TC1', $this->taxCategory->getCode());
    }

    public function testShouldBeUnnamedByDefault(): void
    {
        $this->assertNull($this->taxCategory->getName());
    }

    public function testShouldNameBeMutable(): void
    {
        $this->taxCategory->setName('Taxable goods');

        $this->assertSame('Taxable goods', $this->taxCategory->getName());
    }

    public function testShouldNotHaveDescriptionByDefault(): void
    {
        $this->assertNull($this->taxCategory->getDescription());
    }

    public function testShouldDescriptionBeMutable(): void
    {
        $this->taxCategory->setDescription('All taxable goods');

        $this->assertSame('All taxable goods', $this->taxCategory->getDescription());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        $this->assertInstanceOf(DateTimeInterface::class, $this->taxCategory->getCreatedAt());
    }

    public function testShouldNotHaveLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->taxCategory->getUpdatedAt());
    }
}
