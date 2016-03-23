<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Factory\RuleFactoryInterface;
use Sylius\Component\Core\Promotion\Checker\TaxonRuleChecker;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\ItemsFromTaxonTotalRuleChecker;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RuleFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $decoratedFactory)
    {
        $this->beConstructedWith($decoratedFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Factory\RuleFactory');
    }

    function it_implements_rule_factory_interface()
    {
        $this->shouldImplement(RuleFactoryInterface::class);
    }

    function it_uses_decorated_factory_to_create_new_rule_object($decoratedFactory, RuleInterface $rule)
    {
        $decoratedFactory->createNew()->willReturn($rule);

        $this->createNew()->shouldReturn($rule);
    }

    function it_creates_cart_quantity_rule($decoratedFactory, RuleInterface $rule)
    {
        $decoratedFactory->createNew()->willReturn($rule);
        $rule->setType(RuleInterface::TYPE_CART_QUANTITY)->shouldBeCalled();
        $rule->setConfiguration(['count' => 5])->shouldBeCalled();

        $this->createCartQuantity(5)->shouldReturn($rule);
    }

    function it_creates_item_total_rule($decoratedFactory, RuleInterface $rule)
    {
        $decoratedFactory->createNew()->willReturn($rule);
        $rule->setType(RuleInterface::TYPE_ITEM_TOTAL)->shouldBeCalled();
        $rule->setConfiguration(['amount' => 1000])->shouldBeCalled();

        $this->createItemTotal(1000)->shouldReturn($rule);
    }

    function it_creates_taxon_rule($decoratedFactory, RuleInterface $rule)
    {
        $decoratedFactory->createNew()->willReturn($rule);
        $rule->setType(TaxonRuleChecker::TYPE)->shouldBeCalled();
        $rule->setConfiguration(['taxons' => [1, 6]])->shouldBeCalled();

        $this->createTaxon([1, 6])->shouldReturn($rule);
    }

    function it_creates_items_from_taxon_total_rule($decoratedFactory, RuleInterface $rule, TaxonInterface $taxon)
    {
        $decoratedFactory->createNew()->willReturn($rule);
        $rule->setType(ItemsFromTaxonTotalRuleChecker::TYPE)->shouldBeCalled();
        $rule->setConfiguration(['taxon' => $taxon, 'amount' => 1000])->shouldBeCalled();

        $this->createItemsFromTaxonTotal($taxon, 1000)->shouldReturn($rule);
    }
}
