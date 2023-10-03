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
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class SendContactRequestHandlerSpec extends ObjectBehavior
{
    function let(SenderInterface $sender, ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($sender, $channelRepository);
    }

    function it_sends_contact_request(
        ChannelInterface $channel,
        ChannelRepositoryInterface $channelRepository,
        SenderInterface $sender,
    ): void {
        $command = new SendContactRequest('adam@sylius.com', 'message');
        $command->setChannelCode('CODE');
        $command->setLocaleCode('en_US');

        $channelRepository->findOneByCode('CODE')->willReturn($channel);

        $channel->getContactEmail()->willReturn('channel@contact.com');

        $sender->send(
            Emails::CONTACT_REQUEST,
            ['channel@contact.com'],
            [
                'data' => ['message' => 'message', 'email' => 'adam@sylius.com'],
                'channel' => $channel,
                'localeCode' => 'en_US',
            ],
            [],
            ['adam@sylius.com'],
        );

        $this($command);
    }

    function it_throws_an_exception_if_channel_has_not_been_found(ChannelRepositoryInterface $channelRepository): void
    {
        $command = new SendContactRequest('adam@sylius.com', 'message');
        $command->setChannelCode('CODE');

        $channelRepository->findOneByCode('CODE')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$command])
        ;
    }
}
