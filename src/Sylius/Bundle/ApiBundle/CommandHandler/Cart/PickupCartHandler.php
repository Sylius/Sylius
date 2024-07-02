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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use Doctrine\Persistence\ObjectManager;
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
use Webmozart\Assert\Assert;

final class PickupCartHandler implements MessageHandlerInterface
{
    public function __construct(
        private OrderFactoryInterface $cartFactory,
        private OrderRepositoryInterface $cartRepository,
        private ChannelRepositoryInterface $channelRepository,
        private ObjectManager $orderManager,
        private RandomnessGeneratorInterface $generator,
        private CustomerRepositoryInterface $customerRepository,
        private int $tokenLength = 64,
    ) {
    }

    public function __invoke(PickupCart $pickupCart): OrderInterface
    {
        $channel = $this->channelRepository->findOneByCode($pickupCart->getChannelCode());
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $customer = null;
        if ($pickupCart->getEmail() !== null) {
            /** @var CustomerInterface|null $customer */
            $customer = $this->customerRepository->findOneBy(['email' => $pickupCart->getEmail()]);
        }

        if (null === $customer) {
            return $this->createCart($channel, null, $pickupCart);
        }

        $activeCart = $this->cartRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, $customer);

        if (null === $activeCart) {
            return $this->createCart($channel, $customer, $pickupCart);
        }

        if (null === $activeCart->getTokenValue()) {
            $activeCart->setTokenValue($pickupCart->tokenValue ?? $this->generateTokenValue());
            $this->orderManager->persist($activeCart);
        }

        return $activeCart;
    }

    private function createCart(ChannelInterface $channel, ?CustomerInterface $customer, PickupCart $pickupCart): OrderInterface
    {
        $cart = $this->cartFactory->createNewCart(
            $channel,
            $customer,
            $this->getLocaleCode($pickupCart->getLocaleCode(), $channel),
            $pickupCart->tokenValue ?? $this->generateTokenValue(),
        );

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

    private function generateTokenValue(): string
    {
        return $this->generator->generateUriSafeString($this->tokenLength);
    }
}
