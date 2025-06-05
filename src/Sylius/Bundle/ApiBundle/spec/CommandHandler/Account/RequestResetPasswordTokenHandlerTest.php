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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Account\RequestResetPasswordToken;
use Sylius\Bundle\ApiBundle\Command\Account\SendResetPasswordEmail;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestResetPasswordTokenHandler;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class RequestResetPasswordTokenHandlerTest extends TestCase
{
    /** @var UserRepositoryInterface|MockObject */
    private MockObject $userRepositoryMock;

    /** @var MessageBusInterface|MockObject */
    private MockObject $messageBusMock;

    /** @var GeneratorInterface|MockObject */
    private MockObject $generatorMock;

    /** @var ClockInterface|MockObject */
    private MockObject $clockMock;

    private RequestResetPasswordTokenHandler $requestResetPasswordTokenHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->messageBusMock = $this->createMock(MessageBusInterface::class);
        $this->generatorMock = $this->createMock(GeneratorInterface::class);
        $this->clockMock = $this->createMock(ClockInterface::class);
        $this->requestResetPasswordTokenHandler = new RequestResetPasswordTokenHandler($this->userRepositoryMock, $this->messageBusMock, $this->generatorMock, $this->clockMock);
    }

    public function testHandlesRequestForPasswordResetToken(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        $this->userRepositoryMock->expects(self::once())->method('findOneByEmail')->with('test@email.com')->willReturn($shopUserMock);
        $this->clockMock->expects(self::once())->method('now')->willReturn(new DateTimeImmutable());
        $this->generatorMock->expects(self::once())->method('generate')->willReturn('TOKEN');
        $shopUserMock->expects(self::once())->method('setPasswordResetToken')->with('TOKEN');
        $shopUserMock->setPasswordRequestedAt(Argument::type(DateTimeImmutable::class));
        $sendResetPasswordEmail = new SendResetPasswordEmail('test@email.com', 'WEB', 'en_US');
        $this->messageBusMock->expects(self::once())->method('dispatch')->with($sendResetPasswordEmail, [new DispatchAfterCurrentBusStamp()])->willReturn(new Envelope($sendResetPasswordEmail));
        $requestResetPasswordToken = new RequestResetPasswordToken(
            channelCode: 'WEB',
            localeCode: 'en_US',
            email: 'test@email.com',
        );
        $this($requestResetPasswordToken);
    }

    public function testDoesNothingWhenShopUserHasNotBeenFound(): void
    {
        $this->userRepositoryMock->expects(self::once())->method('findOneByEmail')->with('test@email.com')->willReturn(null);
        $this->messageBusMock->expects(self::never())->method('dispatch');
        $requestResetPasswordToken = new RequestResetPasswordToken(
            channelCode: 'WEB',
            localeCode: 'en_US',
            email: 'test@email.com',
        );
        $this($requestResetPasswordToken);
    }
}
