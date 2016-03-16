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
use Sylius\Component\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionStorageSpec extends ObjectBehavior
{
    function let(SessionInterface $session)
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Storage\SessionStorage');
    }

    function it_implements_Sylius_storage_interface()
    {
        $this->shouldImplement(StorageInterface::class);
    }

    function it_gets_default_data_if_session_was_not_started($session)
    {
        $session->isStarted()->willReturn(false);
        $session->get('key', 'default')->willReturn('default');

        $this->getData('key', 'default')->shouldReturn('default');
    }

    function it_gets_default_data_if_no_record_was_found($session)
    {
        $session->isStarted()->willReturn(true);
        $session->get('key', 'default')->willReturn('default');

        $this->getData('key', 'default')->shouldReturn('default');
    }

    function it_gets_data_if_found($session)
    {
        $session->isStarted()->willReturn(true);
        $session->get('key', 'default')->willReturn('data');

        $this->getData('key', 'default')->shouldReturn('data');
    }

    function it_sets_data($session)
    {
        $session->set('key', 'data')->shouldBeCalled();

        $this->setData('key', 'data');
    }
}
