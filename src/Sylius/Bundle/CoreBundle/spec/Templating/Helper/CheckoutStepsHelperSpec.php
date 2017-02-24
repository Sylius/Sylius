<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Templating\Helper\CheckoutStepsHelper;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CheckoutStepsHelperSpec extends ObjectBehavior
{
    function let(
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
    ) {
        $this->beConstructedWith(
            $orderPaymentMethodSelectionRequirementChecker,
            $orderShippingMethodSelectionRequirementChecker
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CheckoutStepsHelper::class);
    }

    function it_is_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_checks_if_order_requires_shipping(
        OrderInterface $order,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
    ) {
        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(true);

        $this->isShippingRequired($order)->shouldReturn(true);
    }

    function it_checks_if_order_required_payment(
        OrderInterface $order,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker
    ) {
        $orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order)->willReturn(true);
        $this->isPaymentRequired($order)->shouldReturn(true);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_checkout_steps');
    }

}
