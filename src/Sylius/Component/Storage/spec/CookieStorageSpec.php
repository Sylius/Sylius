<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Storage;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class CookieStorageSpec extends ObjectBehavior
{
    function let(RequestStack $requestStack, Request $request, ParameterBag $cookies)
    {
        $requestStack->getMasterRequest()->willReturn($request);
        $request->cookies = $cookies;

        $this->beConstructedWith($requestStack);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Storage\CookieStorage');
    }

    function it_is_a_Sylius_storage()
    {
        $this->shouldHaveType(StorageInterface::class);
    }

    function it_checks_if_it_has_stored_data_in_a_cookie(ParameterBag $cookies)
    {
        $cookies->has('filling')->willReturn(true);

        $this->hasData('filling')->shouldBe(true);
    }

    function it_gets_data_stored_in_a_cookie(ParameterBag $cookies)
    {
        $cookies->get('filling', null)->willReturn('cream');

        $this->getData('filling')->shouldReturn('cream');
    }

    function it_returns_a_default_if_no_data_was_found_in_a_cookie(ParameterBag $cookies)
    {
        $cookies->get('filling', 'chocolate')->willReturn('chocolate');

        $this->getData('filling', 'chocolate')->shouldReturn('chocolate');
    }

    function it_stores_data_in_a_cookie(ParameterBag $cookies)
    {
        $cookies->set('filling', 'jam')->shouldBeCalled();

        $this->setData('filling', 'jam');
    }

    function it_removes_data_from_a_cookie(ParameterBag $cookies)
    {
        $cookies->remove('filling')->shouldBeCalled();

        $this->removeData('filling');
    }
}
