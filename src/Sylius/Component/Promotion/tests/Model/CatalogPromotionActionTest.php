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
use Sylius\Component\Promotion\Model\CatalogPromotionAction;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

final class CatalogPromotionActionTest extends TestCase
{
    private CatalogPromotionInterface&MockObject $catalogPromotion;

    private CatalogPromotionAction $catalogPromotionAction;

    protected function setUp(): void
    {
        $this->catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $this->catalogPromotionAction = new CatalogPromotionAction();
    }

    public function testShouldImplementCatalogPromotionActionInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionActionInterface::class, $this->catalogPromotionAction);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->catalogPromotionAction->getId());
    }

    public function testShouldNotHaveTypeByDefault(): void
    {
        $this->assertNull($this->catalogPromotionAction->getType());
    }

    public function testShouldTypeBeMutable(): void
    {
        $this->catalogPromotionAction->setType('percentage_discount');

        $this->assertSame('percentage_discount', $this->catalogPromotionAction->getType());
    }

    public function testShouldInitializeConfigurationArray(): void
    {
        $this->assertSame([], $this->catalogPromotionAction->getConfiguration());
    }

    public function testShouldConfigurationBeEmptyByDefault(): void
    {
        $this->assertEmpty($this->catalogPromotionAction->getConfiguration());
    }

    public function testShouldConfigurationBeMutable(): void
    {
        $this->catalogPromotionAction->setConfiguration(['amount' => 500]);

        $this->assertSame(['amount' => 500], $this->catalogPromotionAction->getConfiguration());
    }

    public function testShouldNotBeAttachedToCatalogPromotionByDefault(): void
    {
        $this->assertNull($this->catalogPromotionAction->getCatalogPromotion());
    }

    public function testShouldAttachItselfToCatalogPromotion(): void
    {
        $this->catalogPromotionAction->setCatalogPromotion($this->catalogPromotion);

        $this->assertSame($this->catalogPromotion, $this->catalogPromotionAction->getCatalogPromotion());
    }

    public function testShouldDetachItselfFromCatalogPromotion(): void
    {
        $this->catalogPromotionAction->setCatalogPromotion($this->catalogPromotion);

        $this->catalogPromotionAction->setCatalogPromotion(null);

        $this->assertNull($this->catalogPromotionAction->getCatalogPromotion());
    }
}
