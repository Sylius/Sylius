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
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleChecker;
use Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class TaxonInPromotionRuleCheckerTest extends TestCase
{
    private MockObject&RepositoryInterface $promotionRuleRepository;

    private MockObject&PromotionRuleInterface $promotionRule;

    private MockObject&TaxonInterface $taxon;

    private TaxonInPromotionRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->promotionRuleRepository = $this->createMock(RepositoryInterface::class);
        $this->promotionRule = $this->createMock(PromotionRuleInterface::class);
        $this->taxon = $this->createMock(TaxonInterface::class);
        $this->ruleChecker = new TaxonInPromotionRuleChecker($this->promotionRuleRepository);
    }

    public function testShouldImplementTotalOfItemsFromTaxonPromotionRuleAppliedCheckerInterface(): void
    {
        $this->assertInstanceOf(TaxonInPromotionRuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldCheckIfPromotionRuleIsAppliedWithTaxon(): void
    {
        $this->promotionRuleRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['type' => 'total_of_items_from_taxon'])
            ->willReturn([$this->promotionRule]);
        $this->promotionRule
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn(['FASHION_WEB' => ['taxon' => 'sample_taxon_code']]);
        $this->taxon->expects($this->once())->method('getCode')->willReturn('sample_taxon_code');

        $this->assertTrue($this->ruleChecker->isInUse($this->taxon));
    }

    public function testShouldReturnFalseWhenPromotionRuleIsNotAppliedWithTaxon(): void
    {
        $this->promotionRuleRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['type' => 'total_of_items_from_taxon'])
            ->willReturn([$this->promotionRule]);
        $this->promotionRule
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn(['FASHION_WEB' => ['taxon' => 'sample_taxon_code']]);
        $this->taxon->expects($this->once())->method('getCode')->willReturn('different_taxon_code');

        $this->assertFalse($this->ruleChecker->isInUse($this->taxon));
    }

    public function testShouldReturnFalseWhenNoPromotionRulesAreFound(): void
    {
        $this->promotionRuleRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['type' => 'total_of_items_from_taxon'])
            ->willReturn([]);

        $this->assertFalse($this->ruleChecker->isInUse($this->taxon));
    }
}
