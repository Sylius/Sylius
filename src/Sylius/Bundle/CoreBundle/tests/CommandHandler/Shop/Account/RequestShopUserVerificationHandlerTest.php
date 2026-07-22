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

namespace Tests\Sylius\Bundle\CoreBundle\CommandHandler\Shop\Account;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Command\Shop\Account\RequestShopUserVerification;
use Sylius\Bundle\CoreBundle\Command\Shop\Account\SendShopUserVerificationEmail;
use Sylius\Bundle\CoreBundle\CommandHandler\Shop\Account\RequestShopUserVerificationHandler;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[CoversClass(RequestShopUserVerificationHandler::class)]
final class RequestShopUserVerificationHandlerTest extends TestCase
{
    /** @var MockObject&UserRepositoryInterface<ShopUserInterface> */
    private MockObject&UserRepositoryInterface $shopUserRepository;

    /** @var ChannelRepositoryInterface<ChannelInterface>&MockObject */
    private ChannelRepositoryInterface&MockObject $channelRepository;

    private GeneratorInterface&MockObject $tokenGenerator;

    private MessageBusInterface&MockObject $commandBus;

    private RequestShopUserVerificationHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopUserRepository = $this->createMock(UserRepositoryInterface::class);
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->tokenGenerator = $this->createMock(GeneratorInterface::class);
        $this->commandBus = $this->createMock(MessageBusInterface::class);
        $this->handler = new RequestShopUserVerificationHandler(
            $this->shopUserRepository,
            $this->channelRepository,
            $this->tokenGenerator,
            $this->commandBus,
        );
    }

    public function testIsAMessageHandler(): void
    {
        $attributes = (new \ReflectionClass($this->handler::class))->getAttributes(AsMessageHandler::class);

        self::assertCount(1, $attributes);
    }

    public function testGeneratesTokenAndDispatchesSendingVerificationEmail(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getEmail')->willReturn('shop@example.com');

        $shopUser = $this->createMock(ShopUserInterface::class);
        $shopUser->method('getCustomer')->willReturn($customer);

        $this->shopUserRepository->method('find')->with(42)->willReturn($shopUser);
        $this->channelRepository->method('findOneByCode')->with('WEB')->willReturn($this->createChannel(true));
        $this->tokenGenerator->expects(self::once())->method('generate')->willReturn('TOKEN');
        $shopUser->expects(self::once())->method('setEmailVerificationToken')->with('TOKEN');

        $command = new SendShopUserVerificationEmail('shop@example.com', 'en_US', 'WEB');
        $this->commandBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($command, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($command))
        ;

        ($this->handler)(new RequestShopUserVerification(42, 'WEB', 'en_US'));
    }

    public function testThrowsAnExceptionWhenShopUserHasNotBeenFound(): void
    {
        $this->shopUserRepository->method('find')->with(42)->willReturn(null);
        $this->tokenGenerator->expects(self::never())->method('generate');
        $this->commandBus->expects(self::never())->method('dispatch');

        self::expectException(\InvalidArgumentException::class);

        ($this->handler)(new RequestShopUserVerification(42, 'WEB', 'en_US'));
    }

    public function testThrowsAnExceptionWhenChannelHasNotBeenFound(): void
    {
        $this->shopUserRepository->method('find')->with(42)->willReturn($this->createMock(ShopUserInterface::class));
        $this->channelRepository->method('findOneByCode')->with('WEB')->willReturn(null);
        $this->tokenGenerator->expects(self::never())->method('generate');
        $this->commandBus->expects(self::never())->method('dispatch');

        self::expectException(\InvalidArgumentException::class);

        ($this->handler)(new RequestShopUserVerification(42, 'WEB', 'en_US'));
    }

    public function testThrowsAnExceptionWhenChannelIsDisabled(): void
    {
        $this->shopUserRepository->method('find')->with(42)->willReturn($this->createMock(ShopUserInterface::class));
        $this->channelRepository->method('findOneByCode')->with('WEB')->willReturn($this->createChannel(false));
        $this->tokenGenerator->expects(self::never())->method('generate');
        $this->commandBus->expects(self::never())->method('dispatch');

        self::expectException(\InvalidArgumentException::class);

        ($this->handler)(new RequestShopUserVerification(42, 'WEB', 'en_US'));
    }

    private function createChannel(bool $enabled): ChannelInterface&MockObject
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('isEnabled')->willReturn($enabled);

        return $channel;
    }
}
