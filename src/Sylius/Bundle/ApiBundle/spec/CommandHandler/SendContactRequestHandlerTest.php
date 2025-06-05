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
    /** @var ChannelRepositoryInterface|MockObject */
    private MockObject $channelRepositoryMock;

    /** @var ContactEmailManagerInterface|MockObject */
    private MockObject $contactEmailManagerMock;

    private SendContactRequestHandler $sendContactRequestHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->channelRepositoryMock = $this->createMock(ChannelRepositoryInterface::class);
        $this->contactEmailManagerMock = $this->createMock(ContactEmailManagerInterface::class);
        $this->sendContactRequestHandler = new SendContactRequestHandler($this->channelRepositoryMock, $this->contactEmailManagerMock);
    }

    public function testSendsContactRequest(): void
    {
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $command = new SendContactRequest(
            channelCode: 'CODE',
            localeCode: 'en_US',
            email: 'adam@sylius.com',
            message: 'message',
        );
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('CODE')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('getContactEmail')->willReturn('channel@contact.com');
        $this->contactEmailManagerMock->sendContactRequest(
            ['message' => 'message', 'email' => 'adam@sylius.com'],
            ['channel@contact.com'],
            $channelMock,
            'en_US',
        );
        $this($command);
    }

    public function testThrowsAnExceptionIfChannelHasNotBeenFound(): void
    {
        $command = new SendContactRequest(
            channelCode: 'CODE',
            localeCode: 'en_US',
            email: 'adam@sylius.com',
            message: 'message',
        );
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('CODE')->willReturn(null);
        $this->expectException(ChannelNotFoundException::class);
        $this->sendContactRequestHandler->__invoke($command);
    }
}
