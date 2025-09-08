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
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountRegistrationEmail;
use Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyShopUserHandler;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class VerifyShopUserHandlerTest extends TestCase
{
    private MockObject&RepositoryInterface $shopUserRepository;

    private ClockInterface&MockObject $clock;

    private MessageBusInterface&MockObject $commandBus;

    private VerifyShopUserHandler $handler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopUserRepository = $this->createMock(RepositoryInterface::class);
        $this->clock = $this->createMock(ClockInterface::class);
        $this->commandBus = $this->createMock(MessageBusInterface::class);
        $this->handler = new VerifyShopUserHandler($this->shopUserRepository, $this->clock, $this->commandBus);
    }

    public function testVerifiesShopUser(): void
    {
        /** @var UserInterface|MockObject $user */
        $user = $this->createMock(UserInterface::class);
        $this->shopUserRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['emailVerificationToken' => 'ToKeN'])
            ->willReturn($user);
        $this->clock->expects(self::once())->method('now')->willReturn(new \DateTimeImmutable());
        $user->expects(self::once())->method('getEmail')->willReturn('shop@example.com');
        $user->expects(self::once())
            ->method('setVerifiedAt')
            ->with($this->isInstanceOf(\DateTimeImmutable::class));
        $user->expects(self::once())->method('setEmailVerificationToken')->with(null);
        $user->expects(self::once())->method('enable');
        $this->commandBus->expects(self::once())
            ->method('dispatch')
            ->with(
                new SendAccountRegistrationEmail('shop@example.com', 'en_US', 'WEB'),
                [new DispatchAfterCurrentBusStamp()],
            )->willReturn(new Envelope(new \stdClass()));
        $this->handler->__invoke(new VerifyShopUser(channelCode: 'WEB', localeCode:  'en_US', token: 'ToKeN'));
    }

    public function testThrowsErrorIfUserDoesNotExist(): void
    {
        $this->shopUserRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['emailVerificationToken' => 'ToKeN'])
            ->willReturn(null);
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke(new VerifyShopUser(channelCode: 'WEB', localeCode:  'en_US', token: 'ToKeN'));
    }
}
