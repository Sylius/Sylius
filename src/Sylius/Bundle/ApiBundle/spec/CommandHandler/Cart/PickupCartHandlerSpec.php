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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\CoreBundle\Factory\OrderFactoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PickupCartHandlerSpec extends ObjectBehavior
{
    private const TOKEN_LENGTH = 20;

    function let(
        OrderFactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator,
        CustomerRepositoryInterface $customerRepository,
    ): void {
        $this->beConstructedWith(
            $cartFactory,
            $cartRepository,
            $channelRepository,
            $orderManager,
            $generator,
            $customerRepository,
            self::TOKEN_LENGTH,
        );
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_picks_up_a_new_cart_for_logged_in_shop_user(
        OrderFactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        CustomerInterface $customer,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator,
        CustomerRepositoryInterface $customerRepository,
        OrderInterface $cart,
        ChannelInterface $channel,
        LocaleInterface $locale,
    ): void {
        $pickupCart = new PickupCart(channelCode: 'code', email: 'sample@email.com');

        $channelRepository->findOneByCode('code')->willReturn($channel);
        $channel->getDefaultLocale()->willReturn($locale);

        $customerRepository->findOneBy(['email' => 'sample@email.com'])->willReturn($customer);

        $cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, $customer)->willReturn(null);

        $generator->generateUriSafeString(self::TOKEN_LENGTH)->willReturn('urisafestr');
        $locale->getCode()->willReturn('en_US');

        $channel->getLocales()->willReturn(new ArrayCollection([$locale->getWrappedObject()]));

        $cartFactory->createNewCart($channel, $customer, 'en_US', 'urisafestr')->willReturn($cart);
        $orderManager->persist($cart)->shouldBeCalled();

        $this($pickupCart);
    }

    function it_picks_up_a_new_cart_for_logged_in_shop_user_when_the_user_has_no_default_address(
        OrderFactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        CustomerInterface $customer,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator,
        CustomerRepositoryInterface $customerRepository,
        OrderInterface $cart,
        ChannelInterface $channel,
        LocaleInterface $locale,
    ): void {
        $pickupCart = new PickupCart(channelCode: 'code', email: 'sample@email.com');

        $channelRepository->findOneByCode('code')->willReturn($channel);
        $channel->getDefaultLocale()->willReturn($locale);

        $customerRepository->findOneBy(['email' => 'sample@email.com'])->willReturn($customer);

        $cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, $customer)->willReturn(null);

        $generator->generateUriSafeString(self::TOKEN_LENGTH)->willReturn('urisafestr');
        $locale->getCode()->willReturn('en_US');

        $channel->getLocales()->willReturn(new ArrayCollection([$locale->getWrappedObject()]));

        $cartFactory->createNewCart($channel, $customer, 'en_US', 'urisafestr')->willReturn($cart);
        $orderManager->persist($cart)->shouldBeCalled();

        $this($pickupCart);
    }

    function it_picks_up_an_existing_cart_with_token_for_logged_in_shop_user(
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer,
        ObjectManager $orderManager,
        OrderInterface $cart,
        ChannelInterface $channel,
    ): void {
        $pickupCart = new PickupCart(channelCode: 'code', email: 'sample@email.com');

        $channelRepository->findOneByCode('code')->willReturn($channel);

        $customerRepository->findOneBy(['email' => 'sample@email.com'])->willReturn($customer);

        $cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, $customer)->willReturn($cart);
        $cart->getTokenValue()->willReturn('token');

        $orderManager->persist(Argument::any())->shouldNotBeCalled();

        $this($pickupCart);
    }

    function it_picks_up_an_existing_cart_without_token_for_logged_in_shop_user(
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer,
        ObjectManager $orderManager,
        OrderInterface $cart,
        ChannelInterface $channel,
        RandomnessGeneratorInterface $generator,
    ): void {
        $pickupCart = new PickupCart(channelCode: 'code', email: 'sample@email.com');

        $channelRepository->findOneByCode('code')->willReturn($channel);

        $customerRepository->findOneBy(['email' => 'sample@email.com'])->willReturn($customer);

        $generator->generateUriSafeString(self::TOKEN_LENGTH)->willReturn('urisafestr');

        $cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, $customer)->willReturn($cart);
        $orderManager->persist($cart);

        $this($pickupCart);
    }

    function it_picks_up_a_cart_for_visitor(
        OrderFactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator,
        OrderInterface $cart,
        ChannelInterface $channel,
        LocaleInterface $locale,
    ): void {
        $pickupCart = new PickupCart(channelCode: 'code');

        $channelRepository->findOneByCode('code')->willReturn($channel);
        $channel->getDefaultLocale()->willReturn($locale);

        $cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, Argument::any())->shouldNotBeCalled(null);

        $generator->generateUriSafeString(self::TOKEN_LENGTH)->willReturn('urisafestr');
        $locale->getCode()->willReturn('en_US');

        $channel->getLocales()->willReturn(new ArrayCollection([$locale->getWrappedObject()]));

        $cartFactory->createNewCart($channel, null, 'en_US', 'urisafestr')->willReturn($cart);
        $orderManager->persist($cart)->shouldBeCalled();

        $this($pickupCart);
    }

    function it_picks_up_a_cart_with_locale_code_for_visitor(
        OrderFactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator,
        OrderInterface $cart,
        ChannelInterface $channel,
        LocaleInterface $locale,
    ): void {
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'en_US');

        $channelRepository->findOneByCode('code')->willReturn($channel);
        $channel->getDefaultLocale()->willReturn($locale);
        $locale->getCode()->willReturn('en_US');
        $channel->getLocales()->willReturn(new ArrayCollection([$locale->getWrappedObject()]));

        $cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, Argument::any())->shouldNotBeCalled(null);

        $generator->generateUriSafeString(self::TOKEN_LENGTH)->willReturn('urisafestr');
        $locale->getCode()->willReturn('en_US');

        $cartFactory->createNewCart($channel, null, 'en_US', 'urisafestr')->willReturn($cart);
        $orderManager->persist($cart)->shouldBeCalled();

        $this($pickupCart);
    }

    function it_throws_exception_if_locale_code_is_not_correct(
        OrderFactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        RandomnessGeneratorInterface $generator,
        OrderInterface $cart,
        ChannelInterface $channel,
        LocaleInterface $locale,
    ): void {
        $pickupCart = new PickupCart(channelCode: 'code', localeCode: 'ru_RU');

        $channelRepository->findOneByCode('code')->willReturn($channel);
        $channel->getDefaultLocale()->willReturn($locale);
        $locale->getCode()->willReturn('en_US');
        $locales = new ArrayCollection([]);
        $channel->getLocales()->willReturn($locales);

        $cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, Argument::any())->shouldNotBeCalled(null);

        $generator->generateUriSafeString(self::TOKEN_LENGTH)->willReturn('urisafestr');

        $cartFactory->createNewCart($channel, null, 'en_US', 'urisafestr')->willReturn($cart);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$pickupCart])
        ;
    }
}
