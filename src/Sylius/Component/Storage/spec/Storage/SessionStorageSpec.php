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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionStorageSpec extends ObjectBehavior
{
    public function let(SessionInterface $session)
    {
        $this->beConstructedWith($session);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Storage\SessionStorage');
    }

    public function it_implements_Sylius_storage_interface()
    {
        $this->shouldImplement('Sylius\Component\Storage\StorageInterface');
    }

    public function it_gets_default_data_if_session_was_not_started($session)
    {
        $session->isStarted()->willReturn(false);
        $session->get('key', 'default')->willReturn('default');

        $this->getData('key', 'default')->shouldReturn('default');
    }

    public function it_gets_default_data_if_no_record_was_found($session)
    {
        $session->isStarted()->willReturn(true);
        $session->get('key', 'default')->willReturn('default');

        $this->getData('key', 'default')->shouldReturn('default');
    }

    public function it_gets_data_if_found($session)
    {
        $session->isStarted()->willReturn(true);
        $session->get('key', 'default')->willReturn('data');

        $this->getData('key', 'default')->shouldReturn('data');
    }

    public function it_sets_data($session)
    {
        $session->set('key', 'data')->shouldBeCalled();

        $this->setData('key', 'data');
    }
}
