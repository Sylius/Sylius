<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Mailer\Sender;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SenderSpec extends ObjectBehavior
{
    function let(AdapterInterface $adapter, EmailProviderInterface $provider)
    {
        $this->beConstructedWith($adapter, $provider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Mailer\Sender\Sender');
    }

    function it_sends_an_email_through_the_adapter($adapter, $provider, EmailInterface $email)
    {
        $provider->getEmail('bar')->shouldBeCalled()->willReturn($email);
        $email->isEnabled()->shouldBeCalled()->willReturn(true);

        $adapter->send($email, array('jonh@example.com'), array('foo' => 2))->shouldBeCalled();

        $this->send('bar', array('jonh@example.com'), array('foo' => 2));
    }

    function it_does_not_send_disabled_emails($adapter, $provider, EmailInterface $email)
    {
        $provider->getEmail('bar')->shouldBeCalled()->willReturn($email);
        $email->isEnabled()->shouldBeCalled()->willReturn(false);

        $adapter->send()->shouldNotBeCalled();

        $this->send('bar', array('jonh@example.com'), array('foo' => 2));
    }
}
