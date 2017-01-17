<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Checkout;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGenerator;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGeneratorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CheckoutStateUrlGeneratorSpec extends ObjectBehavior
{
    function let(RouterInterface $router)
    {
        $routeCollection = [
            'addressed' => [
                'route' => 'sylius_shop_checkout_select_shipping',
            ],
        ];

        $this->beConstructedWith($router, $routeCollection);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CheckoutStateUrlGenerator::class);
    }

    function it_is_a_url_generator()
    {
        $this->shouldImplement(UrlGeneratorInterface::class);
    }

    function it_is_a_checkout_state_url_generator()
    {
        $this->shouldImplement(CheckoutStateUrlGeneratorInterface::class);
    }

    function it_generates_state_url(RouterInterface $router, OrderInterface $order)
    {
        $order->getCheckoutState()->willReturn('addressed');

        $router->generate('sylius_shop_checkout_select_shipping', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/checkout/address')
        ;

        $this->generateForOrderCheckoutState($order)->shouldReturn('/checkout/address');
    }

    function it_is_a_regular_url_generator(RouterInterface $router)
    {
        $router->generate('route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH)->willReturn('/some-route');

        $this->generate('route_name')->shouldReturn('/some-route');
    }

    function it_throws_route_not_found_exception_if_there_is_no_route_for_given_state(
        RouterInterface $router,
        OrderInterface $order
    ) {
        $order->getCheckoutState()->willReturn('shipping_selected');
        $router->generate(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(RouteNotFoundException::class)->during('generateForOrderCheckoutState', [$order]);
    }
}
