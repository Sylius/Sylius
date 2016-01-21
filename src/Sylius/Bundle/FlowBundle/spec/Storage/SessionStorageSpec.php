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
use Prophecy\Argument;
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
    }

    function it_is_a_Sylius_storage()
    {
        $this->shouldImplement('Sylius\Component\Storage\StorageInterface');
    }

    function it_is_a_Sylius_Flow_storage()
    {
        $this->shouldImplement('Sylius\Bundle\FlowBundle\Storage\StorageInterface');
    }

    function it_is_a_Sylius_session_storage()
    {
        $this->shouldImplement('Sylius\Component\Storage\SessionStorage');
    }

    function itd_message_is_mutable(SessionFlowsBag $bag)
    {
        $bag->set('domain/message_key', 'message_value')->shouldBeCalled();

        $this->setData('message_key', 'message_value');
    }

    function it_has_message(SessionFlowsBag $bag)
    {
        $bag->get('domain/message_key', null)->shouldBeCalled();

        $this->getData('message_key');
    }

    function it_checks_is_the_message_exists(SessionFlowsBag $bag)
    {
        $bag->has('domain/message_key')->shouldBeCalled();

        $this->hasData('message_key');
    }

    function it_removes_a_message(SessionFlowsBag $bag)
    {
        $bag->remove('domain/message_key')->shouldBeCalled();

        $this->removeData('message_key');
    }

    function it_removes_all_messages(SessionFlowsBag $bag)
    {
        $bag->remove('domain/message_key')->shouldBeCalled();

        $this->removeData('message_key');
    }
}
