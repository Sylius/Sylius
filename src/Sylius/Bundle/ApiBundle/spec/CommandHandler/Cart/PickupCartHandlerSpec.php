<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PickupCartHandlerSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $cartFactory,
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
        );
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_picks_up_a_new_cart_for_logged_in_shop_user(
        FactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        CustomerInterface $customer,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator,
        CustomerRepositoryInterface $customerRepository,
        OrderInterface $cart,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        LocaleInterface $locale,
    ): void {
        $pickupCart = new PickupCart();
        $pickupCart->setChannelCode('code');
        $pickupCart->setEmail('sample@email.com');

        $channelRepository->findOneByCode('code')->willReturn($channel);
        $channel->getBaseCurrency()->willReturn($currency);
        $channel->getDefaultLocale()->willReturn($locale);

        $customerRepository->findOneBy(['email' => 'sample@email.com'])->willReturn($customer);

        $cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, $customer)->willReturn(null);

        $generator->generateUriSafeString(10)->willReturn('urisafestr');
        $currency->getCode()->willReturn('USD');
        $locale->getCode()->willReturn('en_US');

        $channel->getLocales()->willReturn(new ArrayCollection([$locale->getWrappedObject()]));

        $cartFactory->createNew()->willReturn($cart);
        $cart->setCustomerWithAuthorization($customer)->shouldBeCalled();
        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setCurrencyCode('USD')->shouldBeCalled();
        $cart->setLocaleCode('en_US')->shouldBeCalled();
        $cart->setTokenValue('urisafestr')->shouldBeCalled();

        $orderManager->persist($cart)->shouldBeCalled();

        $this($pickupCart);
    }

    function it_picks_up_an_existing_cart_for_logged_in_shop_user(
        FactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer,
        ObjectManager $orderManager,
        OrderInterface $cart,
        ChannelInterface $channel,
    ): void {
        $pickupCart = new PickupCart();
        $pickupCart->setChannelCode('code');
        $pickupCart->setEmail('sample@email.com');

        $channelRepository->findOneByCode('code')->willReturn($channel);

        $customerRepository->findOneBy(['email' => 'sample@email.com'])->willReturn($customer);

        $cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, $customer)->willReturn($cart);

        $cartFactory->createNew()->willReturn($cart);
        $cart->setCustomer($customer)->shouldNotBeCalled();
        $cart->setCreatedByGuest(false)->shouldNotBeCalled();
        $cart->setChannel($channel)->shouldNotBeCalled();

        $orderManager->persist($cart)->shouldNotBeCalled();

        $this($pickupCart);
    }

    function it_picks_up_a_cart_for_visitor(
        FactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator,
        OrderInterface $cart,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        LocaleInterface $locale,
    ): void {
        $pickupCart = new PickupCart();
        $pickupCart->setChannelCode('code');

        $channelRepository->findOneByCode('code')->willReturn($channel);
        $channel->getBaseCurrency()->willReturn($currency);
        $channel->getDefaultLocale()->willReturn($locale);

        $cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, Argument::any())->shouldNotBeCalled(null);

        $generator->generateUriSafeString(10)->willReturn('urisafestr');
        $currency->getCode()->willReturn('USD');
        $locale->getCode()->willReturn('en_US');

        $channel->getLocales()->willReturn(new ArrayCollection([$locale->getWrappedObject()]));

        $cartFactory->createNew()->willReturn($cart);
        $cart->setCustomer(Argument::any())->shouldNotBeCalled();
        $cart->setCreatedByGuest(false)->shouldNotBeCalled();
        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setCurrencyCode('USD')->shouldBeCalled();
        $cart->setLocaleCode('en_US')->shouldBeCalled();
        $cart->setTokenValue('urisafestr')->shouldBeCalled();

        $orderManager->persist($cart)->shouldBeCalled();

        $this($pickupCart);
    }

    function it_picks_up_a_cart_with_locale_code_for_visitor(
        FactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator,
        OrderInterface $cart,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        LocaleInterface $locale,
    ): void {
        $pickupCart = new PickupCart();
        $pickupCart->setChannelCode('code');
        $pickupCart->localeCode = 'en_US';

        $channelRepository->findOneByCode('code')->willReturn($channel);
        $channel->getBaseCurrency()->willReturn($currency);
        $channel->getDefaultLocale()->willReturn($locale);
        $locale->getCode()->willReturn('en_US');
        $channel->getLocales()->willReturn(new ArrayCollection([$locale->getWrappedObject()]));

        $cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, Argument::any())->shouldNotBeCalled(null);

        $generator->generateUriSafeString(10)->willReturn('urisafestr');
        $currency->getCode()->willReturn('USD');
        $locale->getCode()->willReturn('en_US');

        $cartFactory->createNew()->willReturn($cart);
        $cart->setCustomer(Argument::any())->shouldNotBeCalled();
        $cart->setCreatedByGuest(false)->shouldNotBeCalled();
        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setCurrencyCode('USD')->shouldBeCalled();
        $cart->setLocaleCode('en_US')->shouldBeCalled();
        $cart->setTokenValue('urisafestr')->shouldBeCalled();

        $orderManager->persist($cart)->shouldBeCalled();

        $this($pickupCart);
    }

    function it_throws_exception_if_locale_code_is_not_correct(
        FactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        RandomnessGeneratorInterface $generator,
        OrderInterface $cart,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        LocaleInterface $locale,
    ): void {
        $pickupCart = new PickupCart();
        $pickupCart->setChannelCode('code');
        $pickupCart->localeCode = 'ru_RU';

        $channelRepository->findOneByCode('code')->willReturn($channel);
        $channel->getBaseCurrency()->willReturn($currency);
        $channel->getDefaultLocale()->willReturn($locale);
        $locale->getCode()->willReturn('en_US');
        $locales = new ArrayCollection([]);
        $channel->getLocales()->willReturn($locales);

        $cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, Argument::any())->shouldNotBeCalled(null);

        $generator->generateUriSafeString(10)->willReturn('urisafestr');
        $currency->getCode()->willReturn('USD');

        $cartFactory->createNew()->willReturn($cart);
        $cart->setCustomer(Argument::any())->shouldNotBeCalled();
        $cart->setCreatedByGuest(false)->shouldNotBeCalled();
        $cart->setChannel($channel)->shouldBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$pickupCart])
        ;
    }
}
