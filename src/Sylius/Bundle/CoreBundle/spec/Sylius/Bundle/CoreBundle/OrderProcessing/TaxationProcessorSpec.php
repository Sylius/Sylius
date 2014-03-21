<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxationProcessorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $adjustmentRepository,
        CalculatorInterface $calculator,
        TaxRateResolverInterface $taxRateResolver,
        ZoneMatcherInterface $zoneMatcher,
        Settings $taxationSettings
    )
    {
        $this->beConstructedWith($adjustmentRepository, $calculator, $taxRateResolver, $zoneMatcher, $taxationSettings);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\TaxationProcessor');
    }

    function it_implements_Sylius_taxation_processor_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\OrderProcessing\TaxationProcessorInterface');
    }

    function it_doesnt_apply_any_taxes_if_order_has_no_items(OrderInterface $order)
    {
        $order->getItems()->willReturn(array());
        $order->removeTaxAdjustments()->shouldBeCalled();
        $order->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->applyTaxes($order);
    }

    function it_removes_existing_tax_adjustments(OrderInterface $order)
    {
        $order->getItems()->willReturn(array());
        $order->removeTaxAdjustments()->shouldBeCalled();

        $this->applyTaxes($order);
    }
}
