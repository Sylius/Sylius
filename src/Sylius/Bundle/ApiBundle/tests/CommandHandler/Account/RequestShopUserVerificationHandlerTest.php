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
use Sylius\Bundle\ApiBundle\Command\Account\RequestShopUserVerification;
use Sylius\Bundle\ApiBundle\Command\Account\SendShopUserVerificationEmail;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestShopUserVerificationHandler;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class RequestShopUserVerificationHandlerTest extends TestCase
{
    private MockObject&UserRepositoryInterface $userRepository;

    private GeneratorInterface&MockObject $generator;

    private MessageBusInterface&MockObject $messageBus;

    private RequestShopUserVerificationHandler $handler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->generator = $this->createMock(GeneratorInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->handler = new RequestShopUserVerificationHandler(
            $this->userRepository,
            $this->generator,
            $this->messageBus,
        );
    }

    public function testThrowsExceptionIfShopUserDoesNotExist(): void
    {
        $this->userRepository->expects(self::once())->method('find')->with(42)->willReturn(null);
        $resendVerificationEmail = new RequestShopUserVerification(
            shopUserId: 42,
            channelCode: 'WEB',
            localeCode: 'en_US',
        );
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($resendVerificationEmail);
    }

    public function testHandlesRequestForResendVerificationEmail(): void
    {
        /** @var ShopUserInterface|MockObject $shopUser */
        $shopUser = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        $this->userRepository->expects(self::once())->method('find')->with(42)->willReturn($shopUser);
        $shopUser->expects(self::once())->method('getCustomer')->willReturn($customer);
        $customer->expects(self::once())->method('getEmail')->willReturn('test@email.com');
        $this->generator->expects(self::once())->method('generate')->willReturn('TOKEN');
        $shopUser->expects(self::once())->method('setEmailVerificationToken')->with('TOKEN');
        $sendAccountVerificationEmail = new SendShopUserVerificationEmail('test@email.com', 'en_US', 'WEB');
        $this->messageBus->expects(self::once())
            ->method('dispatch')
            ->with($sendAccountVerificationEmail, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($sendAccountVerificationEmail));
        $resendVerificationEmail = new RequestShopUserVerification(
            shopUserId: 42,
            channelCode: 'WEB',
            localeCode: 'en_US',
        );
        $this->handler->__invoke($resendVerificationEmail);
    }
}
