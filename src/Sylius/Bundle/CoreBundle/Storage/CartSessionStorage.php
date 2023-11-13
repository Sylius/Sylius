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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;

final class CartSessionStorage implements CartStorageInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private string $sessionKeyName,
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function hasForChannel(ChannelInterface $channel): bool
    {
        try {
            return $this->requestStack->getSession()->has($this->getCartKeyName($channel));
        } catch (SessionNotFoundException) {
            return false;
        }
    }

    public function getForChannel(ChannelInterface $channel): ?OrderInterface
    {
        if ($this->hasForChannel($channel)) {
            $cartId = $this->requestStack->getSession()->get($this->getCartKeyName($channel));

            return $this->orderRepository->findCartByChannel($cartId, $channel);
        }

        return null;
    }

    public function setForChannel(ChannelInterface $channel, OrderInterface $cart): void
    {
        $this->requestStack->getSession()->set($this->getCartKeyName($channel), $cart->getId());
    }

    public function removeForChannel(ChannelInterface $channel): void
    {
        $this->requestStack->getSession()->remove($this->getCartKeyName($channel));
    }

    private function getCartKeyName(ChannelInterface $channel): string
    {
        return sprintf('%s.%s', $this->sessionKeyName, $channel->getCode());
    }
}
