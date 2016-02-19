<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Behat\Page\Order;
 
use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Page\SymfonyPage;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PayOrderPageSpec extends ObjectBehavior
{
    function let(RouterInterface $router, Session $session)
    {
        $this->beConstructedWith($session, [], $router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Page\Order\PayOrderPage');
    }

    function it_is_symfony_page_object()
    {
        $this->shouldHaveType(SymfonyPage::class);
    }

    function it_has_route_name()
    {
        $this->getRouteName()->shouldReturn('sylius_account_order_payment_index');
    }
}
