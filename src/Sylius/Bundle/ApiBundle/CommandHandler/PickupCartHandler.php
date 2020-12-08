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

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\AdminApiBundle\Model\UserInterface;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental */
final class PickupCartHandler implements MessageHandlerInterface
{
    /** @var FactoryInterface */
    private $cartFactory;

    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var UserContextInterface */
    private $userContext;

    /** @var ObjectManager */
    private $orderManager;

    /** @var RandomnessGeneratorInterface */
    private $generator;

    public function __construct(
        FactoryInterface $cartFactory,
        OrderRepositoryInterface $cartRepository,
        ChannelRepositoryInterface $channelRepository,
        UserContextInterface $userContext,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator
    ) {
        $this->cartFactory = $cartFactory;
        $this->cartRepository = $cartRepository;
        $this->channelRepository = $channelRepository;
        $this->userContext = $userContext;
        $this->orderManager = $orderManager;
        $this->generator = $generator;
    }

    public function __invoke(PickupCart $pickupCart)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($pickupCart->getChannelCode());

        $customer = $this->provideCustomer();
        if ($customer !== null) {
            /** @var OrderInterface|null $cart */
            $cart = $this->cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, $customer);
            if ($cart !== null) {
                return $cart;
            }
        }

        /** @var OrderInterface $cart */
        $cart = $this->cartFactory->createNew();

        /** @var LocaleInterface $locale */
        $locale = $channel->getDefaultLocale();

        /** @var string $localeCode */
        $localeCode = $locale->getCode();

        /** @var string|null $commandLocaleCode */
        $commandLocaleCode = $pickupCart->localeCode;

        if ($commandLocaleCode !== null && $channel->hasLocaleWithLocaleCode($commandLocaleCode)) {
            $localeCode = $commandLocaleCode;
        }

        /** @var CurrencyInterface $currency */
        $currency = $channel->getBaseCurrency();

        $cart->setChannel($channel);
        $cart->setLocaleCode($localeCode);
        $cart->setCurrencyCode($currency->getCode());
        $cart->setTokenValue($pickupCart->tokenValue ?? $this->generator->generateUriSafeString(10));
        if ($customer !== null) {
            $cart->setCustomer($customer);
        }

        $this->orderManager->persist($cart);

        return $cart;
    }

    private function provideCustomer(): ?CustomerInterface
    {
        /** @var UserInterface|null $user */
        $user = $this->userContext->getUser();
        if ($user !== null && $user instanceof ShopUserInterface) {
            return $user->getCustomer();
        }

        return null;
    }
}
