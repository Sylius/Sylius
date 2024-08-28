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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Bundle\CoreBundle\Mailer\ContactEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class SendContactRequestHandlerSpec extends ObjectBehavior
{
    function let(
        ChannelRepositoryInterface $channelRepository,
        ContactEmailManagerInterface $contactEmailManager,
    ): void {
        $this->beConstructedWith($channelRepository, $contactEmailManager);
    }

    function it_sends_contact_request(
        ChannelRepositoryInterface $channelRepository,
        ContactEmailManagerInterface $contactEmailManager,
        ChannelInterface $channel,
    ): void {
        $command = new SendContactRequest(
            channelCode: 'CODE',
            localeCode: 'en_US',
            email: 'adam@sylius.com',
            message: 'message',
        );

        $channelRepository->findOneByCode('CODE')->willReturn($channel);

        $channel->getContactEmail()->willReturn('channel@contact.com');

        $contactEmailManager->sendContactRequest(
            ['message' => 'message', 'email' => 'adam@sylius.com'],
            ['channel@contact.com'],
            $channel,
            'en_US',
        );

        $this($command);
    }

    function it_throws_an_exception_if_channel_has_not_been_found(ChannelRepositoryInterface $channelRepository): void
    {
        $command = new SendContactRequest(
            channelCode: 'CODE',
            localeCode: 'en_US',
            email: 'adam@sylius.com',
            message: 'message',
        );

        $channelRepository->findOneByCode('CODE')->willReturn(null);

        $this
            ->shouldThrow(ChannelNotFoundException::class)
            ->during('__invoke', [$command])
        ;
    }
}
