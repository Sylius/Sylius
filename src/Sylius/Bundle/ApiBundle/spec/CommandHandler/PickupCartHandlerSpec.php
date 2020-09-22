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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PickupCartHandlerSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $cartFactory,
        ChannelContextInterface $channelContext,
        UserContextInterface $userContext,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator

    ): void {
        $this->beConstructedWith(
            $cartFactory,
            $channelContext,
            $userContext,
            $orderManager,
            $generator
        );
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_picks_up_a_cart_for_logged_shop_user(
        FactoryInterface $cartFactory,
        ChannelContextInterface $channelContext,
        UserContextInterface $userContext,
        ShopUserInterface $user,
        CustomerInterface $customer,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator,
        OrderInterface $cart,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        LocaleInterface $locale
    ): void {
        $cartFactory->createNew()->willReturn($cart);
        $channelContext->getChannel()->willReturn($channel);
        $generator->generateUriSafeString(10)->willReturn('urisafestr');

        $channel->getBaseCurrency()->willReturn($currency);
        $channel->getDefaultLocale()->willReturn($locale);
        $userContext->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $cart->setCustomer($customer)->shouldBeCalled();

        $currency->getCode()->willReturn('USD');

        $locale->getCode()->willReturn('en_US');

        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setCurrencyCode('USD')->shouldBeCalled();
        $cart->setLocaleCode('en_US')->shouldBeCalled();
        $cart->setTokenValue('urisafestr')->shouldBeCalled();

        $orderManager->persist($cart)->shouldBeCalled();

        $this(new PickupCart());
    }

    function it_picks_up_a_cart_for_visitor(
        FactoryInterface $cartFactory,
        ChannelContextInterface $channelContext,
        UserContextInterface $userContext,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator,
        OrderInterface $cart,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        LocaleInterface $locale
    ): void {
        $cartFactory->createNew()->willReturn($cart);
        $channelContext->getChannel()->willReturn($channel);
        $generator->generateUriSafeString(10)->willReturn('urisafestr');

        $channel->getBaseCurrency()->willReturn($currency);
        $channel->getDefaultLocale()->willReturn($locale);
        $userContext->getUser()->willReturn(null);

        $currency->getCode()->willReturn('USD');

        $locale->getCode()->willReturn('en_US');

        $cart->setChannel($channel)->shouldBeCalled();
        $cart->setCurrencyCode('USD')->shouldBeCalled();
        $cart->setLocaleCode('en_US')->shouldBeCalled();
        $cart->setTokenValue('urisafestr')->shouldBeCalled();

        $orderManager->persist($cart)->shouldBeCalled();

        $this(new PickupCart());
    }
}
