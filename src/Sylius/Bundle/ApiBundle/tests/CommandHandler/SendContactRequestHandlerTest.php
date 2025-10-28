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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Bundle\ApiBundle\CommandHandler\SendContactRequestHandler;
use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Bundle\CoreBundle\Mailer\ContactEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class SendContactRequestHandlerTest extends TestCase
{
    private ChannelRepositoryInterface&MockObject $channelRepository;

    private ContactEmailManagerInterface&MockObject $contactEmailManager;

    private SendContactRequestHandler $handler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->contactEmailManager = $this->createMock(ContactEmailManagerInterface::class);
        $this->handler = new SendContactRequestHandler($this->channelRepository, $this->contactEmailManager);
    }

    public function testSendsContactRequest(): void
    {
        /** @var ChannelInterface|MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        $command = new SendContactRequest(
            channelCode: 'CODE',
            localeCode: 'en_US',
            email: 'adam@sylius.com',
            message: 'message',
        );
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('CODE')
            ->willReturn($channel);
        $channel->expects(self::once())->method('getContactEmail')->willReturn('channel@contact.com');
        $this->contactEmailManager->sendContactRequest(
            ['message' => 'message', 'email' => 'adam@sylius.com'],
            ['channel@contact.com'],
            $channel,
            'en_US',
        );
        $this->handler->__invoke($command);
    }

    public function testThrowsAnExceptionIfChannelHasNotBeenFound(): void
    {
        $command = new SendContactRequest(
            channelCode: 'CODE',
            localeCode: 'en_US',
            email: 'adam@sylius.com',
            message: 'message',
        );
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('CODE')
            ->willReturn(null);
        self::expectException(ChannelNotFoundException::class);
        $this->handler->__invoke($command);
    }
}
