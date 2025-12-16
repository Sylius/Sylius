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

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Model\CatalogPromotion;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;

final class CatalogPromotionTest extends TestCase
{
    private CatalogPromotionScopeInterface&MockObject $catalogPromotionScope;

    private CatalogPromotionActionInterface&MockObject $catalogPromotionAction;

    private CatalogPromotion $catalogPromotion;

    protected function setUp(): void
    {
        $this->catalogPromotionScope = $this->createMock(CatalogPromotionScopeInterface::class);
        $this->catalogPromotionAction = $this->createMock(CatalogPromotionActionInterface::class);
        $this->catalogPromotion = new CatalogPromotion();
        $this->catalogPromotion->setCurrentLocale('pl_PL');
        $this->catalogPromotion->setFallbackLocale('pl_PL');
    }

    public function testShouldImplementCatalogPromotionInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionInterface::class, $this->catalogPromotion);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->catalogPromotion->getId());
    }

    public function testShouldCodeBeMutable(): void
    {
        $this->catalogPromotion->setCode('mugs_discount');

        $this->assertSame('mugs_discount', $this->catalogPromotion->getCode());
    }

    public function testShouldNameBeMutable(): void
    {
        $this->catalogPromotion->setName('Mugs Discount');

        $this->assertSame('Mugs Discount', $this->catalogPromotion->getName());
    }

    public function testShouldLabelBeMutable(): void
    {
        $this->catalogPromotion->setLabel('Mugs Discount');

        $this->assertSame('Mugs Discount', $this->catalogPromotion->getLabel());
    }

    public function testShouldDescriptionBeMutable(): void
    {
        $this->catalogPromotion->setDescription('Discount on every mug.');

        $this->assertSame('Discount on every mug.', $this->catalogPromotion->getDescription());
    }

    public function testShouldStartDateBeMutable(): void
    {
        $date = new \DateTime('2025-01-01');

        $this->catalogPromotion->setStartDate($date);

        $this->assertSame($date, $this->catalogPromotion->getStartDate());
    }

    public function testShouldEndDateBeMutable(): void
    {
        $date = new \DateTime('2025-01-01');

        $this->catalogPromotion->setEndDate($date);

        $this->assertSame($date, $this->catalogPromotion->getEndDate());
    }

    public function testShouldPriorityBeMutable(): void
    {
        $this->catalogPromotion->setPriority(200);

        $this->assertSame(200, $this->catalogPromotion->getPriority());
    }

    public function testShouldInitializeScopesCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->catalogPromotion->getScopes());
    }

    public function testShouldScopesBeEmptyByDefault(): void
    {
        $this->assertTrue($this->catalogPromotion->getScopes()->isEmpty());
    }

    public function testShouldAddScopes(): void
    {
        $this->catalogPromotionScope->expects($this->once())->method('setCatalogPromotion')->with($this->catalogPromotion);

        $this->catalogPromotion->addScope($this->catalogPromotionScope);

        $this->assertTrue($this->catalogPromotion->hasScope($this->catalogPromotionScope));
    }

    public function testShouldRemoveScopes(): void
    {
        $this->catalogPromotion->addScope($this->catalogPromotionScope);
        $this->catalogPromotionScope->expects($this->once())->method('setCatalogPromotion')->with(null);

        $this->catalogPromotion->removeScope($this->catalogPromotionScope);

        $this->assertFalse($this->catalogPromotion->hasScope($this->catalogPromotionScope));
    }

    public function testShouldActionsCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->catalogPromotion->getActions());
    }

    public function testShouldActionsBeEmptyByDefault(): void
    {
        $this->assertTrue($this->catalogPromotion->getActions()->isEmpty());
    }

    public function testShouldAddActions(): void
    {
        $this->catalogPromotionAction->expects($this->once())->method('setCatalogPromotion')->with($this->catalogPromotion);

        $this->catalogPromotion->addAction($this->catalogPromotionAction);

        $this->assertTrue($this->catalogPromotion->hasAction($this->catalogPromotionAction));
    }

    public function testShouldRemoveActions(): void
    {
        $this->catalogPromotion->addAction($this->catalogPromotionAction);
        $this->catalogPromotionAction->expects($this->once())->method('setCatalogPromotion')->with(null);

        $this->catalogPromotion->removeAction($this->catalogPromotionAction);

        $this->assertFalse($this->catalogPromotion->hasAction($this->catalogPromotionAction));
    }
}
