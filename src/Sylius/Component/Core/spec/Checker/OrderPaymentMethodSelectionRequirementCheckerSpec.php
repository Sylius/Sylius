<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementChecker;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderPaymentMethodSelectionRequirementCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPaymentMethodSelectionRequirementChecker::class);
    }

    function it_implements_order_payment_necessity_checker_interface()
    {
        $this->shouldImplement(OrderPaymentMethodSelectionRequirementCheckerInterface::class);
    }

    function it_says_that_payment_method_have_to_be_selected_if_order_total_is_bigger_than_0(OrderInterface $order)
    {
        $order->getTotal()->willReturn(1000);

        $this->isPaymentMethodSelectionRequired($order)->shouldReturn(true);
    }

    function it_says_that_payment_method_do_not_have_to_be_selected_if_order_total_is_0(OrderInterface $order)
    {
        $order->getTotal()->willReturn(0);

        $this->isPaymentMethodSelectionRequired($order)->shouldReturn(false);
    }
}
