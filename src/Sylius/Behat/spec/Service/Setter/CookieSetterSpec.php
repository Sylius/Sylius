<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Session;
use Behat\Symfony2Extension\Driver\KernelDriver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Behat\Service\Setter\CookieSetter;
use Sylius\Behat\Service\Setter\CookieSetterInterface;

/**
 * @mixin CookieSetter
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CookieSetterSpec extends ObjectBehavior
{
    function let(Session $minkSession)
    {
        $this->beConstructedWith($minkSession, ['base_url' => 'http://localhost:8080/']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Service\Setter\CookieSetter');
    }

    function it_implements_cookie_setter_interface()
    {
        $this->shouldImplement(CookieSetterInterface::class);
    }

    function it_just_sets_a_cookie_if_using_kernel_driver(Session $minkSession, KernelDriver $kernelDriver)
    {
        $minkSession->getDriver()->willReturn($kernelDriver);

        $minkSession->setCookie('abc', 'def')->shouldBeCalled();

        $this->setCookie('abc', 'def');
    }

    function it_sets_a_cookie_if_not_using_kernel_driver_and_driver_is_currently_at_base_url(
        Session $minkSession,
        DriverInterface $driver
    ) {
        $minkSession->getDriver()->willReturn($driver);
        $minkSession->getCurrentUrl()->willReturn('http://localhost:8080/random/site');

        $minkSession->setCookie('abc', 'def')->shouldBeCalled();

        $this->setCookie('abc', 'def');
    }

    function it_loads_base_url_and_sets_a_cookie_if_not_using_kernel_driver_and_driver_is_currently_outside_base_url(
        Session $minkSession,
        DriverInterface $driver
    ) {
        $minkSession->getDriver()->willReturn($driver);
        $minkSession->getCurrentUrl()->willReturn('http://sylius.org');

        $minkSession->visit('http://localhost:8080/')->shouldBeCalled();
        $minkSession->setCookie('abc', 'def')->shouldBeCalled();

        $this->setCookie('abc', 'def');
    }
}
