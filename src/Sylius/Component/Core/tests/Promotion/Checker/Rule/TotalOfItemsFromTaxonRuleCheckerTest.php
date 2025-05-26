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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class TotalOfItemsFromTaxonRuleCheckerTest extends TestCase
{
    private MockObject&TaxonRepositoryInterface $taxonRepository;

    private ChannelInterface&MockObject $channel;

    private MockObject&OrderInterface $order;

    private MockObject&OrderItemInterface $compositeBowItem;

    private MockObject&OrderItemInterface $longswordItem;

    private MockObject&OrderItemInterface $reflexBowItem;

    private MockObject&ProductInterface $compositeBow;

    private MockObject&ProductInterface $longsword;

    private MockObject&ProductInterface $reflexBow;

    private MockObject&TaxonInterface $bows;

    private TotalOfItemsFromTaxonRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->taxonRepository = $this->createMock(TaxonRepositoryInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->compositeBowItem = $this->createMock(OrderItemInterface::class);
        $this->longswordItem = $this->createMock(OrderItemInterface::class);
        $this->reflexBowItem = $this->createMock(OrderItemInterface::class);
        $this->compositeBow = $this->createMock(ProductInterface::class);
        $this->longsword = $this->createMock(ProductInterface::class);
        $this->reflexBow = $this->createMock(ProductInterface::class);
        $this->bows = $this->createMock(TaxonInterface::class);
        $this->ruleChecker = new TotalOfItemsFromTaxonRuleChecker($this->taxonRepository);
    }

    public function testShouldImplementRuleCheckerInterface(): void
    {
        $this->assertInstanceOf(RuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldRecognizeSubjectAsEligibleIfItHasItemsFromConfiguredTaxonWhichHasRequiredTotal(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([
            $this->compositeBowItem,
            $this->longswordItem,
            $this->reflexBowItem,
        ]));
        $this->taxonRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'bows'])
            ->willReturn($this->bows);
        $this->compositeBowItem->expects($this->once())->method('getProduct')->willReturn($this->compositeBow);
        $this->compositeBow->expects($this->once())->method('hasTaxon')->with($this->bows)->willReturn(true);
        $this->compositeBowItem->expects($this->once())->method('getTotal')->willReturn(5000);
        $this->longswordItem->expects($this->once())->method('getProduct')->willReturn($this->longsword);
        $this->longsword->expects($this->once())->method('hasTaxon')->with($this->bows)->willReturn(false);
        $this->reflexBowItem->expects($this->once())->method('getProduct')->willReturn($this->reflexBow);
        $this->reflexBow->expects($this->once())->method('hasTaxon')->with($this->bows)->willReturn(true);
        $this->reflexBowItem->expects($this->once())->method('getTotal')->willReturn(9000);

        $this->assertTrue(
            $this->ruleChecker->isEligible($this->order, ['WEB_US' => ['taxon' => 'bows', 'amount' => 10000]]),
        );
    }

    public function testShouldRecognizeSubjectAsEligibleIfItHasItemsFromConfiguredTaxonWhichHasTotalEqualWithRequired(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([
            $this->compositeBowItem,
            $this->reflexBowItem,
        ]));
        $this->taxonRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'bows'])
            ->willReturn($this->bows);
        $this->compositeBowItem->expects($this->once())->method('getProduct')->willReturn($this->compositeBow);
        $this->compositeBow->expects($this->once())->method('hasTaxon')->with($this->bows)->willReturn(true);
        $this->compositeBowItem->expects($this->once())->method('getTotal')->willReturn(5000);
        $this->reflexBowItem->expects($this->once())->method('getProduct')->willReturn($this->reflexBow);
        $this->reflexBow->expects($this->once())->method('hasTaxon')->with($this->bows)->willReturn(true);
        $this->reflexBowItem->expects($this->once())->method('getTotal')->willReturn(5000);

        $this->assertTrue(
            $this->ruleChecker->isEligible($this->order, ['WEB_US' => ['taxon' => 'bows', 'amount' => 10000]]),
        );
    }

    public function testShouldNotRecognizeSubjectAsEligibleIfItemsFromRequiredTaxonHasTooLowTotal(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([
            $this->compositeBowItem,
            $this->longswordItem,
        ]));
        $this->taxonRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'bows'])
            ->willReturn($this->bows);
        $this->compositeBowItem->expects($this->once())->method('getProduct')->willReturn($this->compositeBow);
        $this->compositeBow->expects($this->once())->method('hasTaxon')->with($this->bows)->willReturn(true);
        $this->compositeBowItem->expects($this->once())->method('getTotal')->willReturn(5000);
        $this->longswordItem->expects($this->once())->method('getProduct')->willReturn($this->longsword);
        $this->longsword->expects($this->once())->method('hasTaxon')->with($this->bows)->willReturn(false);

        $this->assertFalse(
            $this->ruleChecker->isEligible($this->order, ['WEB_US' => ['taxon' => 'bows', 'amount' => 10000]]),
        );
    }

    public function testShouldReturnFalseIfConfigurationIsInvalid(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');

        $this->assertFalse($this->ruleChecker->isEligible($this->order, ['WEB_US' => ['amount' => 4000]]));
    }

    public function testShouldReturnFalseIfThereIsNoConfigurationForOrderChannel(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');

        $this->assertFalse($this->ruleChecker->isEligible($this->order, []));
    }

    public function testShouldReturnFalseIfTaxonWithConfiguredCodeCannotBeFound(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->taxonRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'sniper_rifles'])
            ->willReturn(null);
        $this->assertFalse(
            $this->ruleChecker->isEligible($this->order, ['WEB_US' => ['taxon' => 'sniper_rifles', 'amount' => 1000]]),
        );
    }

    public function testShouldThrowExceptionIfPassedSubjectIsNotOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->ruleChecker->isEligible($this->createMock(PromotionSubjectInterface::class), []);
    }
}
