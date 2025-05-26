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

namespace Tests\Sylius\Component\Core\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Factory\PromotionRuleFactory;
use Sylius\Component\Core\Factory\PromotionRuleFactoryInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\ContainsProductRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\HasTaxonRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\NthOrderRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class PromotionRuleFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $decoratedFactory;

    private MockObject&PromotionRuleInterface $rule;

    private PromotionRuleFactory $factory;

    protected function setUp(): void
    {
        $this->decoratedFactory = $this->createMock(FactoryInterface::class);
        $this->rule = $this->createMock(PromotionRuleInterface::class);
        $this->factory = new PromotionRuleFactory($this->decoratedFactory);
    }

    public function testShouldImplementPromotionRuleFactoryInterface(): void
    {
        $this->assertInstanceOf(PromotionRuleFactoryInterface::class, $this->factory);
    }

    public function testShouldUseDecoratedFactoryToCreateNewRuleObject(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->rule);

        $this->assertSame($this->rule, $this->factory->createNew());
    }

    public function testShouldCreateCartQuantityRule(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->rule);
        $this->rule->expects($this->once())->method('setType')->with(CartQuantityRuleChecker::TYPE);
        $this->rule->expects($this->once())->method('setConfiguration')->with(['count' => 5]);

        $this->assertSame($this->rule, $this->factory->createCartQuantity(5));
    }

    public function testShouldCreateItemTotalRule(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->rule);
        $this->rule->expects($this->once())->method('setType')->with(ItemTotalRuleChecker::TYPE);
        $this->rule->expects($this->once())->method('setConfiguration')->with(['WEB_US' => ['amount' => 1000]]);

        $this->assertSame($this->rule, $this->factory->createItemTotal('WEB_US', 1000));
    }

    public function testShouldCreateHasTaxonRule(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->rule);
        $this->rule->expects($this->once())->method('setType')->with(HasTaxonRuleChecker::TYPE);
        $this->rule->expects($this->once())->method('setConfiguration')->with(['taxons' => [1, 6]]);

        $this->assertSame($this->rule, $this->factory->createHasTaxon([1, 6]));
    }

    public function testShouldCreateTotalOfItemsFromTaxonRule(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->rule);
        $this->rule->expects($this->once())->method('setType')->with(TotalOfItemsFromTaxonRuleChecker::TYPE);
        $this->rule->expects($this->once())->method('setConfiguration')->with(['WEB_US' => ['taxon' => 'spears', 'amount' => 1000]]);

        $this->assertSame($this->rule, $this->factory->createItemsFromTaxonTotal('WEB_US', 'spears', 1000));
    }

    public function testShouldCreateNthOrderRule(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->rule);
        $this->rule->expects($this->once())->method('setType')->with(NthOrderRuleChecker::TYPE);
        $this->rule->expects($this->once())->method('setConfiguration')->with(['nth' => 10]);

        $this->assertSame($this->rule, $this->factory->createNthOrder(10));
    }

    public function testShouldCreateContainsProductRule(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->rule);
        $this->rule->expects($this->once())->method('setType')->with(ContainsProductRuleChecker::TYPE);
        $this->rule->expects($this->once())->method('setConfiguration')->with(['product_code' => '1']);

        $this->assertSame($this->rule, $this->factory->createContainsProduct('1'));
    }
}
