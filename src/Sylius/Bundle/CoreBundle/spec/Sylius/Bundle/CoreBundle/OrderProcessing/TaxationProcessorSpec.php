<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\OrderProcessing;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxationProcessorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $adjustmentRepository,
        CalculatorInterface $calculator,
        TaxRateResolverInterface $taxRateResolver,
        ZoneMatcherInterface $zoneMatcher,
        Settings $taxationSettings
    ) {
        $this->beConstructedWith($adjustmentRepository, $calculator, $taxRateResolver, $zoneMatcher, $taxationSettings);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\OrderProcessing\TaxationProcessor');
    }

    function it_implements_Sylius_taxation_processor_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\OrderProcessing\TaxationProcessorInterface');
    }

    function it_removes_existing_tax_adjustments(OrderInterface $order, Collection $collection)
    {
        $collection->isEmpty()->willReturn(true);

        $order->getItems()->willReturn($collection);
        $order->removeTaxAdjustments()->shouldBeCalled();

        $this->applyTaxes($order);
    }

    function it_doesnt_apply_any_taxes_if_zone_is_missing(
        OrderInterface $order,
        Collection $collection,
        $taxationSettings
    ) {
        $collection->isEmpty()->willReturn(false);

        $order->getItems()->willReturn($collection);
        $order->removeTaxAdjustments()->shouldBeCalled();

        $order->getShippingAddress()->willReturn(null);

        $taxationSettings->has('default_tax_zone')->willReturn(false);

        $order->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->applyTaxes($order);
    }
}
