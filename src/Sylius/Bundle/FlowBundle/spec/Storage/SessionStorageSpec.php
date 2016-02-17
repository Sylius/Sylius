<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FlowBundle\Storage;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FlowBundle\Storage\SessionFlowsBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionStorageSpec extends ObjectBehavior
{
    function let(SessionInterface $session, SessionFlowsBag $bag)
    {
        $session->getBag(SessionFlowsBag::NAME)->willReturn($bag);

        $this->beConstructedWith($session);

        $this->initialize('domain');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Storage\SessionStorage');
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Storage\AbstractStorage');
        $this->shouldImplement('Sylius\Bundle\FlowBundle\Storage\StorageInterface');
    }

    function itd_message_is_mutable($bag)
    {
        $bag->set('domain/message_key', 'message_value')->shouldBeCalled();

        $this->set('message_key', 'message_value');
    }

    function it_has_message($bag)
    {
        $bag->get('domain/message_key', null)->shouldBeCalled();

        $this->get('message_key');
    }

    function it_checks_is_the_message_exists($bag)
    {
        $bag->has('domain/message_key')->shouldBeCalled();

        $this->has('message_key');
    }

    function it_removes_a_message($bag)
    {
        $bag->remove('domain/message_key')->shouldBeCalled();

        $this->remove('message_key');
    }

    function it_removes_all_messages($bag)
    {
        $bag->remove('domain/message_key')->shouldBeCalled();

        $this->remove('message_key');
    }
}
