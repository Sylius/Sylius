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

namespace Tests\Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Command\Admin\Account\RequestResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Command\Admin\Account\SendResetPasswordEmail;
use Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account\RequestResetPasswordEmailHandler;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class RequestResetPasswordEmailHandlerTest extends TestCase
{
    private MockObject&UserRepositoryInterface $userRepository;

    private GeneratorInterface&MockObject $generator;

    private ClockInterface&MockObject $clock;

    private MessageBusInterface&MockObject $messageBus;

    private RequestResetPasswordEmailHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->generator = $this->createMock(GeneratorInterface::class);
        $this->clock = $this->createMock(ClockInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->handler = new RequestResetPasswordEmailHandler(
            $this->userRepository,
            $this->generator,
            $this->clock,
            $this->messageBus,
        );
    }

    public function testItIsAMessageHandler(): void
    {
        $reflection = new \ReflectionClass(RequestResetPasswordEmailHandler::class);
        $attributes = $reflection->getAttributes(AsMessageHandler::class);

        $this->assertCount(1, $attributes);
    }

    public function testItHandlesRequestForPasswordResetToken(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);
        $this->userRepository
            ->method('findOneByEmail')
            ->with('admin@example.com')
            ->willReturn($adminUser)
        ;

        $this->generator->method('generate')->willReturn('sometoken');

        $now = new \DateTimeImmutable();
        $this->clock->method('now')->willReturn($now);

        $adminUser->expects($this->once())->method('getEmail')->willReturn('admin@example.com');
        $adminUser->expects($this->once())->method('getLocaleCode')->willReturn('en_US');
        $adminUser->expects($this->once())->method('setPasswordResetToken')->with('sometoken');
        $adminUser->expects($this->once())->method('setPasswordRequestedAt')->with($now);

        $expectedMessage = new SendResetPasswordEmail('admin@example.com', 'en_US');

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($expectedMessage, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($expectedMessage))
        ;

        ($this->handler)(new RequestResetPasswordEmail('admin@example.com'));
    }

    public function testItDoesNothingWhileHandlingIfUserDoesntExist(): void
    {
        $this->userRepository
            ->method('findOneByEmail')
            ->with('admin@example.com')
            ->willReturn(null)
        ;

        $this->messageBus->expects($this->never())->method('dispatch');

        ($this->handler)(new RequestResetPasswordEmail('admin@example.com'));
    }
}
