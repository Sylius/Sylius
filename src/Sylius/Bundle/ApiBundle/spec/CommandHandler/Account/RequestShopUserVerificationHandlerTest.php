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

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
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

final class RequestShopUserVerificationHandlerTest extends TestCase
{
    /** @var UserRepositoryInterface|MockObject */
    private MockObject $userRepositoryMock;

    /** @var GeneratorInterface|MockObject */
    private MockObject $generatorMock;

    /** @var MessageBusInterface|MockObject */
    private MockObject $messageBusMock;

    private RequestShopUserVerificationHandler $requestShopUserVerificationHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->generatorMock = $this->createMock(GeneratorInterface::class);
        $this->messageBusMock = $this->createMock(MessageBusInterface::class);
        $this->requestShopUserVerificationHandler = new RequestShopUserVerificationHandler($this->userRepositoryMock, $this->generatorMock, $this->messageBusMock);
    }

    public function testThrowsExceptionIfShopUserDoesNotExist(): void
    {
        $this->userRepositoryMock->expects(self::once())->method('find')->with(42)->willReturn(null);
        $resendVerificationEmail = new RequestShopUserVerification(
            shopUserId: 42,
            channelCode: 'WEB',
            localeCode: 'en_US',
        );
        $this->expectException(InvalidArgumentException::class);
        $this->requestShopUserVerificationHandler->__invoke($resendVerificationEmail);
    }

    public function testHandlesRequestForResendVerificationEmail(): void
    {
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $this->userRepositoryMock->expects(self::once())->method('find')->with(42)->willReturn($shopUserMock);
        $shopUserMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $customerMock->expects(self::once())->method('getEmail')->willReturn('test@email.com');
        $this->generatorMock->expects(self::once())->method('generate')->willReturn('TOKEN');
        $shopUserMock->expects(self::once())->method('setEmailVerificationToken')->with('TOKEN');
        $sendAccountVerificationEmail = new SendShopUserVerificationEmail('test@email.com', 'en_US', 'WEB');
        $this->messageBusMock->expects(self::once())->method('dispatch')->with($sendAccountVerificationEmail, [new DispatchAfterCurrentBusStamp()])->willReturn(new Envelope($sendAccountVerificationEmail));
        $resendVerificationEmail = new RequestShopUserVerification(
            shopUserId: 42,
            channelCode: 'WEB',
            localeCode: 'en_US',
        );
        $this($resendVerificationEmail);
    }
}
