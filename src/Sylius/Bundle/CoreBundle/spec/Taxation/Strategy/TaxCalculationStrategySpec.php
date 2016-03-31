<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Taxation\Strategy;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Taxation\Strategy\TaxCalculationStrategy;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Core\Taxation\Strategy\AbstractTaxCalculationStrategy;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;

/**
 * @mixin TaxCalculationStrategy
 *
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class TaxCalculationStrategySpec extends ObjectBehavior
{
    function let(
        OrderTaxesApplicatorInterface $applicatorOne,
        OrderTaxesApplicatorInterface $applicatorTwo,
        SettingsInterface $settings
    ) {
        $this->beConstructedWith('order_items_based', [$applicatorOne, $applicatorTwo], $settings);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Taxation\Strategy\TaxCalculationStrategy');
    }

    function it_extends_abstract_tax_calculation_strategy()
    {
        $this->shouldHaveType(AbstractTaxCalculationStrategy::class);
    }

    function it_implements_a_tax_calculation_strategy_interface()
    {
        $this->shouldImplement(TaxCalculationStrategyInterface::class);
    }

    function it_has_a_type()
    {
        $this->getType()->shouldReturn('order_items_based');
    }

    function it_throws_an_exception_if_any_of_the_applicators_are_not_of_the_correct_type(
        OrderTaxesApplicatorInterface $applicatorOne,
        OrderTaxesApplicatorInterface $applicatorTwo,
        \stdClass $applicatorThree,
        SettingsInterface $settings
    ) {
        $this->beConstructedWith('order_items_based', [$applicatorOne, $applicatorTwo, $applicatorThree], $settings);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_can_be_supported_when_the_default_tax_calculation_strategy_setting_matches_the_strategy_type(
        SettingsInterface $settings,
        OrderInterface $order,
        ZoneInterface $zone
    ) {
        $settings->get('default_tax_calculation_strategy')->willReturn('order_items_based');

        $this->supports($order, $zone)->shouldReturn(true);
    }

    function it_cannot_be_supported_when_the_default_tax_calculation_strategy_setting_does_not_match_the_strategy_type(
        SettingsInterface $settings,
        OrderInterface $order,
        ZoneInterface $zone
    ) {
        $settings->get('default_tax_calculation_strategy')->willReturn('order_item_units_based');

        $this->supports($order, $zone)->shouldReturn(false);
    }

    function it_applies_all_of_the_applicators(
        OrderTaxesApplicatorInterface $applicatorOne,
        OrderTaxesApplicatorInterface $applicatorTwo,
        OrderInterface $order,
        ZoneInterface $zone
    ) {
        $applicatorOne->apply($order, $zone)->shouldBeCalled();
        $applicatorTwo->apply($order, $zone)->shouldBeCalled();

        $this->applyTaxes($order, $zone);
    }
}
