<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Templating\Helper\Helper;

final class CheckoutStepsHelperSpec extends ObjectBehavior
{
    function let(
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
    ): void {
        $this->beConstructedWith(
            $orderPaymentMethodSelectionRequirementChecker,
            $orderShippingMethodSelectionRequirementChecker
        );
    }

    function it_is_helper(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_checks_if_order_requires_shipping(
        OrderInterface $order,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
    ): void {
        $orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)->willReturn(true);

        $this->isShippingRequired($order)->shouldReturn(true);
    }

    function it_checks_if_order_required_payment(
        OrderInterface $order,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker
    ): void {
        $orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order)->willReturn(true);
        $this->isPaymentRequired($order)->shouldReturn(true);
    }

    function it_has_name(): void
    {
        $this->getName()->shouldReturn('sylius_checkout_steps');
    }
}
