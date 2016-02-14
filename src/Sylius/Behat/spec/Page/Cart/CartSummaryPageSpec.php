<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Page\Cart;

use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Page\SymfonyPage;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CartSummaryPageSpec extends ObjectBehavior
{
    function let(Session $session, RouterInterface $router)
    {
        $this->beConstructedWith($session, [], $router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Page\Cart\CartSummaryPage');
    }

    function it_is_symfony_page()
    {
        $this->shouldHaveType(SymfonyPage::class);
    }

    function it_has_route_name()
    {
        $this->getRouteName()->shouldReturn('sylius_cart_summary');
    }
}
