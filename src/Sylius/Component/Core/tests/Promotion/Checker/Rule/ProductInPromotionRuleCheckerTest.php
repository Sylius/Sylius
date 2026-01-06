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

namespace Tests\Sylius\Component\Core\Promotion\Checker\Rule;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleChecker;
use Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class ProductInPromotionRuleCheckerTest extends TestCase
{
    private MockObject&RepositoryInterface $promotionRuleRepository;

    private MockObject&PromotionRuleInterface $promotionRule;

    private MockObject&ProductInterface $product;

    private ProductInPromotionRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->promotionRuleRepository = $this->createMock(RepositoryInterface::class);
        $this->promotionRule = $this->createMock(PromotionRuleInterface::class);
        $this->product = $this->createMock(ProductInterface::class);
        $this->ruleChecker = new ProductInPromotionRuleChecker($this->promotionRuleRepository);
    }

    public function testShouldImplementContainsProductPromotionRuleAppliedCheckerInterface(): void
    {
        $this->assertInstanceOf(ProductInPromotionRuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldCheckIfPromotionRuleIsAppliedWithProduct(): void
    {
        $this->promotionRuleRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['type' => 'contains_product'])
            ->willReturn([$this->promotionRule]);
        $this->promotionRule
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn(['product_code' => 'sample_product_code']);
        $this->product->expects($this->once())->method('getCode')->willReturn('sample_product_code');

        $this->assertTrue($this->ruleChecker->isInUse($this->product));
    }

    public function testShouldReturnFalseWhenPromotionRuleIsNotAppliedWithProduct(): void
    {
        $this->promotionRuleRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['type' => 'contains_product'])
            ->willReturn([$this->promotionRule]);
        $this->promotionRule
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn(['product_code' => 'sample_product_code']);
        $this->product->expects($this->once())->method('getCode')->willReturn('different_product_code');

        $this->assertFalse($this->ruleChecker->isInUse($this->product));
    }

    public function testShouldReturnFalseWhenNoPromotionRulesAreFound(): void
    {
        $this->promotionRuleRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['type' => 'contains_product'])
            ->willReturn([]);

        $this->assertFalse($this->ruleChecker->isInUse($this->product));
    }
}
