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
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Provider\DefaultSettingsProviderInterface;
use Sylius\Component\Mailer\Provider\EmailProvider;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface as RendererAdapterInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface as SenderAdapterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SenderSpec extends ObjectBehavior
{
    function let(
        RendererAdapterInterface $rendererAdapter,
        SenderAdapterInterface $senderAdapter,
        EmailProviderInterface $provider,
        DefaultSettingsProviderInterface $defaultSettingsProvider
    ) {
        $this->beConstructedWith($rendererAdapter, $senderAdapter, $provider, $defaultSettingsProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Mailer\Sender\Sender');
    }

    function it_sends_an_email_through_the_adapter(
        $rendererAdapter,
        $senderAdapter,
        $provider,
        EmailInterface $email,
        RenderedEmail $renderedEmail
    ) {
        $provider->getEmail('bar')->shouldBeCalled()->willReturn($email);
        $email->isEnabled()->shouldBeCalled()->willReturn(true);
        $email->getSenderAddress()->shouldBeCalled();
        $email->getSenderName()->shouldBeCalled();

        $data = ['foo' => 2];

        $rendererAdapter->render($email, ['foo' => 2])->willReturn($renderedEmail);
        $senderAdapter->send(['john@example.com'], null, null, $renderedEmail, $email, $data)->shouldBeCalled();

        $this->send('bar', ['john@example.com'], $data);
    }

    function it_does_not_send_disabled_emails(
        RendererAdapterInterface $rendererAdapter,
        SenderAdapterInterface $senderAdapter,
        EmailProvider $provider,
        EmailInterface $email
    ) {
        $provider->getEmail('bar')->shouldBeCalled()->willReturn($email);
        $email->isEnabled()->shouldBeCalled()->willReturn(false);

        $rendererAdapter->render($email, ['foo' => 2])->shouldNotBeCalled();
        $senderAdapter->send(['john@example.com'], 'mail@sylius.org', 'Sylius Mailer', null, $email, [])->shouldNotBeCalled();

        $this->send('bar', ['john@example.com'], ['foo' => 2]);
    }
}
