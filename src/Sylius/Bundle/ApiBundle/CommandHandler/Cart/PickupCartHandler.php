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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use Doctrine\Persistence\ObjectManager;
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

/** @experimental */
final class PickupCartHandler implements MessageHandlerInterface
{
    public function __construct(
        private FactoryInterface $cartFactory,
        private OrderRepositoryInterface $cartRepository,
        private ChannelRepositoryInterface $channelRepository,
        private ObjectManager $orderManager,
        private RandomnessGeneratorInterface $generator,
        private CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function __invoke(PickupCart $pickupCart)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($pickupCart->getChannelCode());

        $customer = null;

        if ($pickupCart->getEmail() !== null) {
            /** @var CustomerInterface|null $customer */
            $customer = $this->customerRepository->findOneBy(['email' => $pickupCart->getEmail()]);
        }

        if ($customer !== null) {
            /** @var OrderInterface|null $cart */
            $cart = $this->cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, $customer);
            if ($cart !== null) {
                return $cart;
            }
        }

        /** @var OrderInterface $cart */
        $cart = $this->cartFactory->createNew();

        /** @var CurrencyInterface $currency */
        $currency = $channel->getBaseCurrency();

        $cart->setChannel($channel);
        $cart->setLocaleCode($this->getLocaleCode($pickupCart->localeCode, $channel));
        $cart->setCurrencyCode($currency->getCode());
        $cart->setTokenValue($pickupCart->tokenValue ?? $this->generator->generateUriSafeString(10));

        if ($customer !== null) {
            $cart->setCustomerWithAuthorization($customer);
        }

        $this->orderManager->persist($cart);

        return $cart;
    }

    private function hasLocaleWithLocaleCode(ChannelInterface $channel, ?string $localeCode): bool
    {
        $locales = $channel->getLocales();

        $localeWithExpectedCode = $locales->filter(function (LocaleInterface $locale) use ($localeCode): bool {
            return $locale->getCode() === $localeCode;
        });

        return !$localeWithExpectedCode->isEmpty();
    }

    private function getLocaleCode(?string $localeCode, ChannelInterface $channel): string
    {
        if ($localeCode === null) {
            /** @var LocaleInterface $locale */
            $locale = $channel->getDefaultLocale();

            $localeCode = $locale->getCode();
        }

        if (!$this->hasLocaleWithLocaleCode($channel, $localeCode)) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot pick up cart, locale code "%s" does not exist.',
                $localeCode,
            ));
        }

        return $localeCode;
    }
}
