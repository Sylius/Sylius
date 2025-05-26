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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\HasTaxonRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class HasTaxonRuleCheckerTest extends TestCase
{
    private MockObject&OrderInterface $subject;

    private MockObject&OrderItemInterface $item;

    private MockObject&ProductInterface $bastardSword;

    private MockObject&TaxonInterface $swords;

    private HasTaxonRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->subject = $this->createMock(OrderInterface::class);
        $this->item = $this->createMock(OrderItemInterface::class);
        $this->bastardSword = $this->createMock(ProductInterface::class);
        $this->swords = $this->createMock(TaxonInterface::class);
        $this->ruleChecker = new HasTaxonRuleChecker();
    }

    public function testShouldImplementRuleCheckerInterface(): void
    {
        $this->assertInstanceOf(RuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldRecognizeSubjectAsEligibleIfProductTaxonIsMatched(): void
    {
        $this->swords->expects($this->once())->method('getCode')->willReturn('swords');
        $this->bastardSword->expects($this->once())->method('getTaxons')->willReturn(new ArrayCollection([$this->swords]));
        $this->item->expects($this->once())->method('getProduct')->willReturn($this->bastardSword);
        $this->subject->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->item]));

        $this->assertTrue($this->ruleChecker->isEligible($this->subject, ['taxons' => ['swords']]));
    }

    public function testShouldRecognizeSubjectAsEligibleIfProductTaxonIsMatchedToOneOfRequiredTaxons(): void
    {
        $this->swords->expects($this->once())->method('getCode')->willReturn('swords');
        $this->bastardSword->expects($this->once())->method('getTaxons')->willReturn(new ArrayCollection([$this->swords]));
        $this->item->expects($this->once())->method('getProduct')->willReturn($this->bastardSword);
        $this->subject->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->item]));

        $this->assertTrue($this->ruleChecker->isEligible($this->subject, ['taxons' => ['swords', 'axes']]));
    }

    public function testShouldRecognizeSubjectAsNotEligibleIfProductTaxonIsNotMatched(): void
    {
        $this->swords->expects($this->once())->method('getCode')->willReturn('swords');
        $this->bastardSword->expects($this->once())->method('getTaxons')->willReturn(new ArrayCollection([$this->swords]));
        $this->item->expects($this->once())->method('getProduct')->willReturn($this->bastardSword);
        $this->subject->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->item]));

        $this->assertFalse($this->ruleChecker->isEligible($this->subject, ['taxons' => ['bows', 'axes']]));
    }

    public function testShouldDoNothingIfConfigurationIsInvalid(): void
    {
        $this->subject->expects($this->never())->method('getItems');

        $this->ruleChecker->isEligible($this->subject, []);
    }

    public function testShouldThrowExceptionIfPromotionSubjectIsNotOrder(): void
    {
        $this->expectException(UnsupportedTypeException::class);

        $this->ruleChecker->isEligible(
            $this->createMock(PromotionSubjectInterface::class),
            ['taxons' => ['swords', 'axes']],
        );
    }
}
