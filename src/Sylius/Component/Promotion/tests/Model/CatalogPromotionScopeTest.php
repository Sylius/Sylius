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

namespace Tests\Sylius\Component\Promotion\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScope;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;

final class CatalogPromotionScopeTest extends TestCase
{
    private CatalogPromotionInterface&MockObject $catalogPromotion;

    private CatalogPromotionScope $catalogPromotionScope;

    protected function setUp(): void
    {
        $this->catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $this->catalogPromotionScope = new CatalogPromotionScope();
    }

    public function testShouldImplementCatalogPromotionScopeInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionScopeInterface::class, $this->catalogPromotionScope);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->catalogPromotionScope->getId());
    }

    public function testShouldNotHaveTypeByDefault(): void
    {
        $this->assertNull($this->catalogPromotionScope->getType());
    }

    public function testShouldTypeBeMutable(): void
    {
        $this->catalogPromotionScope->setType('scope_type');

        $this->assertSame('scope_type', $this->catalogPromotionScope->getType());
    }

    public function testShouldInitializeConfigurationArray(): void
    {
        $this->assertSame([], $this->catalogPromotionScope->getConfiguration());
    }

    public function testShouldConfigurationBeEmptyByDefault(): void
    {
        $this->assertEmpty($this->catalogPromotionScope->getConfiguration());
    }

    public function testShouldConfigurationBeMutable(): void
    {
        $this->catalogPromotionScope->setConfiguration(['value' => 500]);

        $this->assertSame(['value' => 500], $this->catalogPromotionScope->getConfiguration());
    }

    public function testShouldNotBeAttachedToCatalogPromotionByDefault(): void
    {
        $this->assertNull($this->catalogPromotionScope->getCatalogPromotion());
    }

    public function testShouldAttachItselfToCatalogPromotion(): void
    {
        $this->catalogPromotionScope->setCatalogPromotion($this->catalogPromotion);

        $this->assertSame($this->catalogPromotion, $this->catalogPromotionScope->getCatalogPromotion());
    }

    public function testShouldDetachItselfFromCatalogPromotion(): void
    {
        $this->catalogPromotionScope->setCatalogPromotion($this->catalogPromotion);

        $this->catalogPromotionScope->setCatalogPromotion(null);

        $this->assertNull($this->catalogPromotionScope->getCatalogPromotion());
    }
}
