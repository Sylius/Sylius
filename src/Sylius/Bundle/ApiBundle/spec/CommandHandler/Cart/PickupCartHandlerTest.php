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
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
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

final class PickupCartHandlerTest extends TestCase
{
    /** @var OrderFactoryInterface|MockObject */
    private MockObject $cartFactoryMock;

    /** @var OrderRepositoryInterface|MockObject */
    private MockObject $cartRepositoryMock;

    /** @var ChannelRepositoryInterface|MockObject */
    private MockObject $channelRepositoryMock;

    /** @var ObjectManager|MockObject */
    private MockObject $orderManagerMock;

    /** @var RandomnessGeneratorInterface|MockObject */
    private MockObject $generatorMock;

    /** @var CustomerRepositoryInterface|MockObject */
    private MockObject $customerRepositoryMock;

    private PickupCartHandler $pickupCartHandler;

    use MessageHandlerAttributeTrait;

    private const TOKEN_LENGTH = 20;

    protected function setUp(): void
    {
        $this->cartFactoryMock = $this->createMock(OrderFactoryInterface::class);
        $this->cartRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->channelRepositoryMock = $this->createMock(ChannelRepositoryInterface::class);
        $this->orderManagerMock = $this->createMock(ObjectManager::class);
        $this->generatorMock = $this->createMock(RandomnessGeneratorInterface::class);
        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);
        $this->pickupCartHandler = new PickupCartHandler($this->cartFactoryMock, $this->cartRepositoryMock, $this->channelRepositoryMock, $this->orderManagerMock, $this->generatorMock, $this->customerRepositoryMock, self::TOKEN_LENGTH);
    }

    public function testPicksUpANewCartForLoggedInShopUser(): void
    {
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var LocaleInterface|MockObject $localeMock */
        $localeMock = $this->createMock(LocaleInterface::class);
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US', email: 'sample@email.com');
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('code')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('getDefaultLocale')->willReturn($localeMock);
        $this->customerRepositoryMock->expects(self::once())->method('findOneBy')->with(['email' => 'sample@email.com'])->willReturn($customerMock);
        $this->cartRepositoryMock->expects(self::once())->method('findLatestNotEmptyCartByChannelAndCustomer')->with($channelMock, $customerMock)->willReturn(null);
        $this->generatorMock->expects(self::once())->method('generateUriSafeString')->with(self::TOKEN_LENGTH)->willReturn('urisafestr');
        $localeMock->expects(self::once())->method('getCode')->willReturn('en_US');
        $channelMock->expects(self::once())->method('getLocales')->willReturn(new ArrayCollection([$localeMock]));
        $this->cartFactoryMock->expects(self::once())->method('createNewCart')->with($channelMock, $customerMock, 'en_US', 'urisafestr')->willReturn($cartMock);
        $this->orderManagerMock->expects(self::once())->method('persist')->with($cartMock);
        $this($pickupCart);
    }

    public function testPicksUpANewCartForLoggedInShopUserWhenTheUserHasNoDefaultAddress(): void
    {
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var LocaleInterface|MockObject $localeMock */
        $localeMock = $this->createMock(LocaleInterface::class);
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US', email: 'sample@email.com');
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('code')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('getDefaultLocale')->willReturn($localeMock);
        $this->customerRepositoryMock->expects(self::once())->method('findOneBy')->with(['email' => 'sample@email.com'])->willReturn($customerMock);
        $this->cartRepositoryMock->expects(self::once())->method('findLatestNotEmptyCartByChannelAndCustomer')->with($channelMock, $customerMock)->willReturn(null);
        $this->generatorMock->expects(self::once())->method('generateUriSafeString')->with(self::TOKEN_LENGTH)->willReturn('urisafestr');
        $localeMock->expects(self::once())->method('getCode')->willReturn('en_US');
        $channelMock->expects(self::once())->method('getLocales')->willReturn(new ArrayCollection([$localeMock]));
        $this->cartFactoryMock->expects(self::once())->method('createNewCart')->with($channelMock, $customerMock, 'en_US', 'urisafestr')->willReturn($cartMock);
        $this->orderManagerMock->expects(self::once())->method('persist')->with($cartMock);
        $this($pickupCart);
    }

    public function testPicksUpAnExistingCartWithTokenForLoggedInShopUser(): void
    {
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US', email: 'sample@email.com');
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('code')->willReturn($channelMock);
        $this->customerRepositoryMock->expects(self::once())->method('findOneBy')->with(['email' => 'sample@email.com'])->willReturn($customerMock);
        $this->cartRepositoryMock->expects(self::once())->method('findLatestNotEmptyCartByChannelAndCustomer')->with($channelMock, $customerMock)->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getTokenValue')->willReturn('token');
        $this->orderManagerMock->expects(self::never())->method('persist');
        $this($pickupCart);
    }

    public function testPicksUpAnExistingCartWithoutTokenForLoggedInShopUser(): void
    {
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US', email: 'sample@email.com');
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('code')->willReturn($channelMock);
        $this->customerRepositoryMock->expects(self::once())->method('findOneBy')->with(['email' => 'sample@email.com'])->willReturn($customerMock);
        $this->generatorMock->expects(self::once())->method('generateUriSafeString')->with(self::TOKEN_LENGTH)->willReturn('urisafestr');
        $this->cartRepositoryMock->expects(self::once())->method('findLatestNotEmptyCartByChannelAndCustomer')->with($channelMock, $customerMock)->willReturn($cartMock);
        $this->orderManagerMock->persist($cartMock);
        $this($pickupCart);
    }

    public function testPicksUpACartForVisitor(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var LocaleInterface|MockObject $localeMock */
        $localeMock = $this->createMock(LocaleInterface::class);
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US');
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('code')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('getDefaultLocale')->willReturn($localeMock);
        $this->cartRepositoryMock->expects(self::never())->method('findLatestNotEmptyCartByChannelAndCustomer');
        $this->generatorMock->expects(self::once())->method('generateUriSafeString')->with(self::TOKEN_LENGTH)->willReturn('urisafestr');
        $localeMock->expects(self::once())->method('getCode')->willReturn('en_US');
        $channelMock->expects(self::once())->method('getLocales')->willReturn(new ArrayCollection([$localeMock]));
        $this->cartFactoryMock->expects(self::once())->method('createNewCart')->with($channelMock, null, 'en_US', 'urisafestr')->willReturn($cartMock);
        $this->orderManagerMock->expects(self::once())->method('persist')->with($cartMock);
        $this($pickupCart);
    }

    public function testPicksUpACartWithLocaleCodeForVisitor(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var LocaleInterface|MockObject $localeMock */
        $localeMock = $this->createMock(LocaleInterface::class);
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US');
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('code')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('getDefaultLocale')->willReturn($localeMock);
        $localeMock->expects($this->exactly(2))->method('getCode')->willReturnMap([['en_US'], ['en_US']]);
        $this->cartRepositoryMock->expects(self::never())->method('findLatestNotEmptyCartByChannelAndCustomer');
        $this->generatorMock->expects(self::once())->method('generateUriSafeString')->with(self::TOKEN_LENGTH)->willReturn('urisafestr');
        $localeMock->expects(self::once())->method('getCode')->willReturn('en_US');
        $this->cartFactoryMock->expects(self::once())->method('createNewCart')->with($channelMock, null, 'en_US', 'urisafestr')->willReturn($cartMock);
        $this->orderManagerMock->expects(self::once())->method('persist')->with($cartMock);
        $this($pickupCart);
    }

    public function testThrowsExceptionIfLocaleCodeIsNotCorrect(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var LocaleInterface|MockObject $localeMock */
        $localeMock = $this->createMock(LocaleInterface::class);
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'ru_RU');
        $this->channelRepositoryMock->expects(self::once())->method('findOneByCode')->with('code')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('getDefaultLocale')->willReturn($localeMock);
        $localeMock->expects(self::once())->method('getCode')->willReturn('en_US');
        $locales = new ArrayCollection([]);
        $channelMock->expects(self::once())->method('getLocales')->willReturn($locales);
        $this->cartRepositoryMock->expects(self::never())->method('findLatestNotEmptyCartByChannelAndCustomer');
        $this->generatorMock->expects(self::once())->method('generateUriSafeString')->with(self::TOKEN_LENGTH)->willReturn('urisafestr');
        $this->cartFactoryMock->expects(self::once())->method('createNewCart')->with($channelMock, null, 'en_US', 'urisafestr')->willReturn($cartMock);
        $this->expectException(InvalidArgumentException::class);
        $this->pickupCartHandler->__invoke($pickupCart);
    }
}
