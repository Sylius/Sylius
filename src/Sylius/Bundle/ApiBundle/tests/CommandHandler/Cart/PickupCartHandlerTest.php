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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\CommandHandler\Cart\PickupCartHandler;
use Sylius\Bundle\CoreBundle\Factory\OrderFactoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Generator\RandomnessGeneratorInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class PickupCartHandlerTest extends TestCase
{
    private MockObject&OrderFactoryInterface $cartFactory;

    private MockObject&OrderRepositoryInterface $cartRepository;

    private ChannelRepositoryInterface&MockObject $channelRepository;

    private MockObject&ObjectManager $orderManager;

    private MockObject&RandomnessGeneratorInterface $generator;

    private CustomerRepositoryInterface&MockObject $customerRepository;

    private PickupCartHandler $handler;

    private CustomerInterface&MockObject $customer;

    private MockObject&OrderInterface $cart;

    private ChannelInterface&MockObject $channel;

    private LocaleInterface&MockObject $locale;

    use MessageHandlerAttributeTrait;

    private const TOKEN_LENGTH = 20;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartFactory = $this->createMock(OrderFactoryInterface::class);
        $this->cartRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->orderManager = $this->createMock(ObjectManager::class);
        $this->generator = $this->createMock(RandomnessGeneratorInterface::class);
        $this->customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $this->handler = new PickupCartHandler(
            $this->cartFactory,
            $this->cartRepository,
            $this->channelRepository,
            $this->orderManager,
            $this->generator,
            $this->customerRepository,
            self::TOKEN_LENGTH,
        );
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->cart = $this->createMock(OrderInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->locale = $this->createMock(LocaleInterface::class);
    }

    public function testPicksUpANewCartForLoggedInShopUser(): void
    {
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US', email: 'sample@email.com');
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('code')
            ->willReturn($this->channel);
        $this->customerRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['email' => 'sample@email.com'])
            ->willReturn($this->customer);
        $this->cartRepository->expects(self::once())
            ->method('findLatestNotEmptyCartByChannelAndCustomer')
            ->with($this->channel, $this->customer)
            ->willReturn(null);
        $this->generator->expects(self::once())
            ->method('generateUriSafeString')
            ->with(self::TOKEN_LENGTH)
            ->willReturn('urisafestr');
        $this->locale->expects(self::once())->method('getCode')->willReturn('en_US');
        $this->channel->expects(self::once())->method('getLocales')->willReturn(new ArrayCollection([$this->locale]));
        $this->cartFactory->expects(self::once())
            ->method('createNewCart')
            ->with($this->channel, $this->customer, 'en_US', 'urisafestr')
            ->willReturn($this->cart);
        $this->orderManager->expects(self::once())->method('persist')->with($this->cart);
        $this->handler->__invoke($pickupCart);
    }

    public function testPicksUpANewCartForLoggedInShopUserWhenTheUserHasNoDefaultAddress(): void
    {
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US', email: 'sample@email.com');
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('code')
            ->willReturn($this->channel);
        $this->channel->method('getDefaultLocale')->willReturn($this->locale);
        $this->customerRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['email' => 'sample@email.com'])
            ->willReturn($this->customer);
        $this->cartRepository->expects(self::once())
            ->method('findLatestNotEmptyCartByChannelAndCustomer')
            ->with($this->channel, $this->customer)
            ->willReturn(null);
        $this->generator->expects(self::once())
            ->method('generateUriSafeString')
            ->with(self::TOKEN_LENGTH)
            ->willReturn('urisafestr');
        $this->locale->expects(self::once())->method('getCode')->willReturn('en_US');
        $this->channel->expects(self::once())->method('getLocales')->willReturn(new ArrayCollection([$this->locale]));
        $this->cartFactory->expects(self::once())
            ->method('createNewCart')
            ->with($this->channel, $this->customer, 'en_US', 'urisafestr')
            ->willReturn($this->cart);
        $this->orderManager->expects(self::once())->method('persist')->with($this->cart);
        $this->handler->__invoke($pickupCart);
    }

    public function testPicksUpAnExistingCartWithTokenForLoggedInShopUser(): void
    {
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US', email: 'sample@email.com');
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('code')
            ->willReturn($this->channel);
        $this->customerRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['email' => 'sample@email.com'])
            ->willReturn($this->customer);
        $this->cartRepository->expects(self::once())
            ->method('findLatestNotEmptyCartByChannelAndCustomer')
            ->with($this->channel, $this->customer)
            ->willReturn($this->cart);
        $this->cart->expects(self::once())->method('getTokenValue')->willReturn('token');
        $this->orderManager->expects(self::never())->method('persist');
        $this->handler->__invoke($pickupCart);
    }

    public function testPicksUpAnExistingCartWithoutTokenForLoggedInShopUser(): void
    {
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US', email: 'sample@email.com');
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('code')
            ->willReturn($this->channel);
        $this->customerRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['email' => 'sample@email.com'])
            ->willReturn($this->customer);
        $this->generator->expects(self::once())
            ->method('generateUriSafeString')
            ->with(self::TOKEN_LENGTH)
            ->willReturn('urisafestr');
        $this->cartRepository->expects(self::once())
            ->method('findLatestNotEmptyCartByChannelAndCustomer')
            ->with($this->channel, $this->customer)
            ->willReturn($this->cart);
        $this->orderManager->persist($this->cart);
        $this->handler->__invoke($pickupCart);
    }

    public function testPicksUpACartForVisitor(): void
    {
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US');
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('code')
            ->willReturn($this->channel);
        $this->channel->method('getDefaultLocale')->willReturn($this->locale);
        $this->cartRepository->expects(self::never())->method('findLatestNotEmptyCartByChannelAndCustomer');
        $this->generator->expects(self::once())
            ->method('generateUriSafeString')
            ->with(self::TOKEN_LENGTH)
            ->willReturn('urisafestr');
        $this->locale->expects(self::once())->method('getCode')->willReturn('en_US');
        $this->channel->expects(self::once())->method('getLocales')->willReturn(new ArrayCollection([$this->locale]));
        $this->cartFactory->expects(self::once())
            ->method('createNewCart')
            ->with($this->channel, null, 'en_US', 'urisafestr')
            ->willReturn($this->cart);
        $this->orderManager->expects(self::once())->method('persist')->with($this->cart);
        $this->handler->__invoke($pickupCart);
    }

    public function testPicksUpACartWithLocaleCodeForVisitor(): void
    {
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US');
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('code')
            ->willReturn($this->channel);
        $this->channel->method('getDefaultLocale')->willReturn($this->locale);
        $this->locale->method('getCode')->willReturn('en_US');
        $this->cartRepository->expects(self::never())->method('findLatestNotEmptyCartByChannelAndCustomer');
        $this->generator->expects(self::once())
            ->method('generateUriSafeString')
            ->with(self::TOKEN_LENGTH)
            ->willReturn('urisafestr');
        $this->channel->expects(self::once())
            ->method('getLocales')
            ->willReturn(new \Doctrine\Common\Collections\ArrayCollection([$this->locale]));
        $this->cartFactory->expects(self::once())
            ->method('createNewCart')
            ->with($this->channel, null, 'en_US', 'urisafestr')
            ->willReturn($this->cart);
        $this->orderManager->expects(self::once())->method('persist')->with($this->cart);
        $this->handler->__invoke($pickupCart);
    }

    public function testThrowsExceptionIfLocaleCodeIsNotCorrect(): void
    {
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'ru_RU');
        $this->channelRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('code')
            ->willReturn($this->channel);
        $this->channel->method('getDefaultLocale')->willReturn($this->locale);
        $this->locale->method('getCode')->willReturn('en_US');
        $locales = new ArrayCollection([]);
        $this->channel->expects(self::once())->method('getLocales')->willReturn($locales);
        $this->cartRepository->expects(self::never())->method('findLatestNotEmptyCartByChannelAndCustomer');
        $this->generator->method('generateUriSafeString')
            ->with(self::TOKEN_LENGTH)
            ->willReturn('urisafestr');
        $this->cartFactory->method('createNewCart')
            ->with($this->channel, null, 'en_US', 'urisafestr')
            ->willReturn($this->cart);
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($pickupCart);
    }
}
