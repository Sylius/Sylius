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
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CookieStorageSpec extends ObjectBehavior
{
    function let(RequestStack $requestStack, Request $request, ParameterBag $parameterBag)
    {
        $request->cookies = $parameterBag;
        $requestStack->getCurrentRequest()->willReturn($request);
        $this->beConstructedWith($requestStack);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Storage\CookieStorage');
    }

    function it_implements_sylius_storage_interface()
    {
        $this->shouldImplement('Sylius\Component\Storage\StorageInterface');
    }

    function it_checks_if_data_exists($parameterBag)
    {
        $this->hasData('key');

        $parameterBag->has('key')->shouldBeCalled();
    }

    function it_gets_default_data_if_no_record_was_found($parameterBag)
    {
        $parameterBag->get('key', 'default')->shouldBeCalled()->willReturn('default');

        $this->getData('key', 'default')->shouldReturn('default');
    }

    function it_gets_data_if_found($parameterBag)
    {
        $parameterBag->get('key', null)->shouldBeCalled()->willReturn('data');

        $this->getData('key')->shouldReturn('data');
    }

    function it_sets_data($parameterBag)
    {
        $this->setData('key', 'data');

        $parameterBag->set('key', 'data')->shouldBeCalled();
    }

    function it_removes_data($parameterBag)
    {
        $this->removeData('key');

        $parameterBag->remove('key')->shouldBeCalled();
    }
}
