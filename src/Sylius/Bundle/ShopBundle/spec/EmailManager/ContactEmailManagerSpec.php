<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ShopBundle\EmailManager;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class ContactEmailManagerSpec extends ObjectBehavior
{
    function let(SenderInterface $sender): void
    {
        $this->beConstructedWith($sender);
    }

    function it_implements_a_contact_email_manager_interface(): void
    {
        $this->shouldImplement(ContactEmailManagerInterface::class);
    }

    function it_sends_a_contact_request_email(
        SenderInterface $sender,
        ChannelInterface $channel,
    ): void {
        $sender
            ->send(
                'contact_request',
                ['contact@example.com'],
                [
                    'data' => [
                        'email' => 'customer@example.com',
                        'message' => 'Hello!',
                    ],
                    'channel' => $channel,
                    'localeCode' => 'en_US',
                ],
                [],
                ['customer@example.com'],
            )
            ->shouldBeCalled()
        ;

        $this
            ->sendContactRequest(
                [
                    'email' => 'customer@example.com',
                    'message' => 'Hello!',
                ],
                ['contact@example.com'],
                $channel,
                'en_US',
            )
        ;
    }
}
