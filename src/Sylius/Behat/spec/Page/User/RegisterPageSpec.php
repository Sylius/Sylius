<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Page\User;

use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Page\User\RegisterPageInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class RegisterPageSpec extends ObjectBehavior
{
    function let(Session $session, RouterInterface $router)
    {
        $this->beConstructedWith($session, [], $router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Page\User\RegisterPage');
    }

    function it_implements_register_page_interface()
    {
        $this->shouldImplement(RegisterPageInterface::class);
    }

    function it_is_symfony_page()
    {
        $this->shouldHaveType(SymfonyPage::class);
    }
}
