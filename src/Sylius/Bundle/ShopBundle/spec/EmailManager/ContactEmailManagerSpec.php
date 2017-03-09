<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShopBundle\EmailManager;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManager;
use Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ContactEmailManagerSpec extends ObjectBehavior
{
    function let(SenderInterface $sender)
    {
        $this->beConstructedWith($sender);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContactEmailManager::class);
    }

    function it_implements_a_contact_email_manager_interface()
    {
        $this->shouldImplement(ContactEmailManagerInterface::class);
    }

    function it_sends_a_contact_request_email(SenderInterface $sender)
    {
        $sender
            ->send(
                'contact_request',
                ['contact@example.com'],
                [
                    'data' => [
                        'email' => 'customer@example.com',
                        'message' => 'Hello!',
                    ],
                ]
            )
            ->shouldBeCalled()
        ;

        $this
            ->sendContactRequest(
                [
                    'email' => 'customer@example.com',
                    'message' => 'Hello!',
                ],
                ['contact@example.com']
            )
        ;
    }
}
