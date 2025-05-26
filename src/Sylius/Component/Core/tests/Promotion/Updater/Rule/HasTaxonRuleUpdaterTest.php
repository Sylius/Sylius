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

namespace Tests\Sylius\Component\Core\Promotion\Updater\Rule;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Updater\Rule\HasTaxonRuleUpdater;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class HasTaxonRuleUpdaterTest extends TestCase
{
    private MockObject&RepositoryInterface $promotionRuleRepository;

    private EntityManagerInterface&MockObject $manager;

    private HasTaxonRuleUpdater $ruleUpdater;

    protected function setUp(): void
    {
        $this->promotionRuleRepository = $this->createMock(RepositoryInterface::class);
        $this->manager = $this->createMock(EntityManagerInterface::class);
        $this->ruleUpdater = new HasTaxonRuleUpdater(
            $this->promotionRuleRepository,
            $this->manager,
        );
    }

    public function testShouldRemoveDeletedTaxonFromRulesConfigurations(): void
    {
        $firstPromotionRule = $this->createMock(PromotionRuleInterface::class);
        $secondPromotionRule = $this->createMock(PromotionRuleInterface::class);
        $promotion = $this->createMock(PromotionInterface::class);
        $taxon = $this->createMock(TaxonInterface::class);
        $taxon->expects($this->exactly(3))->method('getCode')->willReturn('toys');
        $this->promotionRuleRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['type' => 'has_taxon'])
            ->willReturn([$firstPromotionRule, $secondPromotionRule]);
        $firstPromotionRule->expects($this->once())->method('getConfiguration')->willReturn(['taxons' => ['mugs', 'toys']]);
        $secondPromotionRule->expects($this->once())->method('getConfiguration')->willReturn(['taxons' => ['mugs']]);
        $firstPromotionRule->expects($this->once())->method('getPromotion')->willReturn($promotion);
        $promotion->expects($this->once())->method('getCode')->willReturn('christmas');
        $firstPromotionRule->expects($this->once())->method('setConfiguration')->with(['taxons' => ['mugs']]);
        $secondPromotionRule->expects($this->never())->method('setConfiguration')->with($this->anything());
        $this->manager->expects($this->once())->method('flush');

        $this->assertEquals(['christmas'], $this->ruleUpdater->updateAfterDeletingTaxon($taxon));
    }
}
