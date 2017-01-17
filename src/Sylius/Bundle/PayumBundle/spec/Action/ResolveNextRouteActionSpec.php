<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PayumBundle\Action;

use Payum\Core\Action\ActionInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Action\ResolveNextRouteAction;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ResolveNextRouteActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ResolveNextRouteAction::class);
    }

    function it_is_a_payum_action()
    {
        $this->shouldImplement(ActionInterface::class);
    }

    function it_resolves_next_route_for_completed_payment(
        ResolveNextRoute $resolveNextRouteRequest,
        PaymentInterface $payment
    ) {
        $resolveNextRouteRequest->getFirstModel()->willReturn($payment);
        $payment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $resolveNextRouteRequest->setRouteName('sylius_shop_order_thank_you')->shouldBeCalled();

        $this->execute($resolveNextRouteRequest);
    }

    function it_resolves_next_route_for_cancelled_payment(
        ResolveNextRoute $resolveNextRouteRequest,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $resolveNextRouteRequest->getFirstModel()->willReturn($payment);
        $payment->getState()->willReturn(PaymentInterface::STATE_CANCELLED);
        $payment->getOrder()->willReturn($order);
        $order->getTokenValue()->willReturn('qwerty');

        $resolveNextRouteRequest->setRouteName('sylius_shop_order_show')->shouldBeCalled();
        $resolveNextRouteRequest->setRouteParameters(['tokenValue' => 'qwerty'])->shouldBeCalled();

        $this->execute($resolveNextRouteRequest);
    }

    function it_resolves_next_route_for_payment_in_cart_state(
        ResolveNextRoute $resolveNextRouteRequest,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $resolveNextRouteRequest->getFirstModel()->willReturn($payment);
        $payment->getState()->willReturn(PaymentInterface::STATE_CART);
        $payment->getOrder()->willReturn($order);
        $order->getTokenValue()->willReturn('qwerty');

        $resolveNextRouteRequest->setRouteName('sylius_shop_order_show')->shouldBeCalled();
        $resolveNextRouteRequest->setRouteParameters(['tokenValue' => 'qwerty'])->shouldBeCalled();

        $this->execute($resolveNextRouteRequest);
    }

    function it_resolves_next_route_for_faild_payment(
        ResolveNextRoute $resolveNextRouteRequest,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $resolveNextRouteRequest->getFirstModel()->willReturn($payment);
        $payment->getState()->willReturn(PaymentInterface::STATE_FAILED);
        $payment->getOrder()->willReturn($order);
        $order->getTokenValue()->willReturn('qwerty');

        $resolveNextRouteRequest->setRouteName('sylius_shop_order_show')->shouldBeCalled();
        $resolveNextRouteRequest->setRouteParameters(['tokenValue' => 'qwerty'])->shouldBeCalled();

        $this->execute($resolveNextRouteRequest);
    }

    function it_resolves_next_route_for_processing_payment(
        ResolveNextRoute $resolveNextRouteRequest,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $resolveNextRouteRequest->getFirstModel()->willReturn($payment);
        $payment->getState()->willReturn(PaymentInterface::STATE_PROCESSING);
        $payment->getOrder()->willReturn($order);
        $order->getTokenValue()->willReturn('qwerty');

        $resolveNextRouteRequest->setRouteName('sylius_shop_order_show')->shouldBeCalled();
        $resolveNextRouteRequest->setRouteParameters(['tokenValue' => 'qwerty'])->shouldBeCalled();

        $this->execute($resolveNextRouteRequest);
    }
}
