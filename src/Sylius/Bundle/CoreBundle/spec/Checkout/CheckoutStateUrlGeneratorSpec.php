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
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @mixin CheckoutStateUrlGenerator
 *
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

    function it_generates_state_url(RouterInterface $router)
    {
        $router->generate('sylius_shop_checkout_select_shipping', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/checkout/address')
        ;

        $this->generate('addressed')->shouldReturn('/checkout/address');
    }

    function it_throws_route_not_found_exception_if_there_is_no_route_for_given_state(RouterInterface $router)
    {
        $router->generate(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(RouteNotFoundException::class)->during('generate', ['shipping_selected']);
    }
}
