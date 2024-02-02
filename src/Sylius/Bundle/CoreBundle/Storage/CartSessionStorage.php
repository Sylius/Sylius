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

namespace Sylius\Bundle\CoreBundle\Storage;

use Sylius\Bundle\CoreBundle\Provider\SessionProvider;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartSessionStorage implements CartStorageInterface
{
    public function __construct(
        private RequestStack|SessionInterface $requestStackOrSession,
        private string $sessionKeyName,
        private OrderRepositoryInterface $orderRepository,
    ) {
        if ($requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.12',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in Sylius 2.0. Pass an instance of %s instead.',
                SessionInterface::class,
                self::class,
                RequestStack::class,
            )
            ;
        }
    }

    public function hasForChannel(ChannelInterface $channel): bool
    {
        try {
            return SessionProvider::getSession($this->requestStackOrSession)->has($this->getCartKeyName($channel));
        } catch (SessionNotFoundException) {
            return false;
        }
    }

    public function getForChannel(ChannelInterface $channel): ?OrderInterface
    {
        if ($this->hasForChannel($channel)) {
            $cartId = SessionProvider::getSession($this->requestStackOrSession)->get($this->getCartKeyName($channel));

            return $this->orderRepository->findCartByChannel($cartId, $channel);
        }

        return null;
    }

    public function setForChannel(ChannelInterface $channel, OrderInterface $cart): void
    {
        SessionProvider::getSession($this->requestStackOrSession)->set($this->getCartKeyName($channel), $cart->getId());
    }

    public function removeForChannel(ChannelInterface $channel): void
    {
        SessionProvider::getSession($this->requestStackOrSession)->remove($this->getCartKeyName($channel));
    }

    private function getCartKeyName(ChannelInterface $channel): string
    {
        return sprintf('%s.%s', $this->sessionKeyName, $channel->getCode());
    }
}
