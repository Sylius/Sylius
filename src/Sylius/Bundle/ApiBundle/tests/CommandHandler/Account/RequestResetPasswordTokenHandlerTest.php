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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class RequestResetPasswordTokenHandlerTest extends TestCase
{
    private MockObject&UserRepositoryInterface $userRepository;

    private MessageBusInterface&MockObject $messageBus;

    private GeneratorInterface&MockObject $generator;

    private ClockInterface&MockObject $clock;

    private RequestResetPasswordTokenHandler $handler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->generator = $this->createMock(GeneratorInterface::class);
        $this->clock = $this->createMock(ClockInterface::class);
        $this->handler = new RequestResetPasswordTokenHandler(
            $this->userRepository,
            $this->messageBus,
            $this->generator,
            $this->clock,
        );
    }

    public function testHandlesRequestForPasswordResetToken(): void
    {
        /** @var ShopUserInterface|MockObject $shopUser */
        $shopUser = $this->createMock(ShopUserInterface::class);
        $this->userRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('test@email.com')
            ->willReturn($shopUser);
        $this->clock->expects(self::once())->method('now')->willReturn(new \DateTimeImmutable());
        $this->generator->expects(self::once())->method('generate')->willReturn('TOKEN');
        $shopUser->expects(self::once())->method('setPasswordResetToken')->with('TOKEN');
        $shopUser->expects(self::once())
            ->method('setPasswordRequestedAt')
            ->with(self::isInstanceOf(\DateTimeImmutable::class));
        $sendResetPasswordEmail = new SendResetPasswordEmail('test@email.com', 'WEB', 'en_US');
        $this->messageBus->expects(self::once())
            ->method('dispatch')
            ->with($sendResetPasswordEmail, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($sendResetPasswordEmail));
        $requestResetPasswordToken = new RequestResetPasswordToken(
            channelCode: 'WEB',
            localeCode: 'en_US',
            email: 'test@email.com',
        );
        $this->handler->__invoke($requestResetPasswordToken);
    }

    public function testDoesNothingWhenShopUserHasNotBeenFound(): void
    {
        $this->userRepository->expects(self::once())->method('findOneByEmail')->with('test@email.com')->willReturn(null);
        $this->messageBus->expects(self::never())->method('dispatch');
        $requestResetPasswordToken = new RequestResetPasswordToken(
            channelCode: 'WEB',
            localeCode: 'en_US',
            email: 'test@email.com',
        );
        $this->handler->__invoke($requestResetPasswordToken);
    }
}
