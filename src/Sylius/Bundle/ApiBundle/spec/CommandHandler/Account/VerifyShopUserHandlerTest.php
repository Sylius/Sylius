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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use stdClass;
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountRegistrationEmail;
use Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyShopUserHandler;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class VerifyShopUserHandlerTest extends TestCase
{
    /** @var RepositoryInterface|MockObject */
    private MockObject $shopUserRepositoryMock;

    /** @var ClockInterface|MockObject */
    private MockObject $clockMock;

    /** @var MessageBusInterface|MockObject */
    private MockObject $commandBusMock;

    private VerifyShopUserHandler $verifyShopUserHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->shopUserRepositoryMock = $this->createMock(RepositoryInterface::class);
        $this->clockMock = $this->createMock(ClockInterface::class);
        $this->commandBusMock = $this->createMock(MessageBusInterface::class);
        $this->verifyShopUserHandler = new VerifyShopUserHandler($this->shopUserRepositoryMock, $this->clockMock, $this->commandBusMock);
    }

    public function testVerifiesShopUser(): void
    {
        /** @var UserInterface|MockObject $userMock */
        $userMock = $this->createMock(UserInterface::class);
        $this->shopUserRepositoryMock->expects(self::once())->method('findOneBy')->with(['emailVerificationToken' => 'ToKeN'])->willReturn($userMock);
        $this->clockMock->expects(self::once())->method('now')->willReturn(new DateTimeImmutable());
        $userMock->expects(self::once())->method('getEmail')->willReturn('shop@example.com');
        $userMock->expects(self::once())->method('setVerifiedAt')->with($this->isInstanceOf(DateTimeImmutable::class));
        $userMock->expects(self::once())->method('setEmailVerificationToken')->with(null);
        $userMock->expects(self::once())->method('enable');
        $this->commandBusMock->expects(self::once())->method('dispatch')->with(new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'), [new DispatchAfterCurrentBusStamp()])->willReturn(new Envelope(new stdClass()));
        $this(new VerifyShopUser(channelCode: 'WEB', localeCode:  'en_US', token: 'ToKeN'));
    }

    public function testThrowsErrorIfUserDoesNotExist(): void
    {
        $this->shopUserRepositoryMock->expects(self::once())->method('findOneBy')->with(['emailVerificationToken' => 'ToKeN'])->willReturn(null);
        $this->expectException(InvalidArgumentException::class);
        $this->verifyShopUserHandler->__invoke(new VerifyShopUser(channelCode: 'WEB', localeCode:  'en_US', token: 'ToKeN'));
    }
}
