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

namespace Sylius\Bundle\CoreBundle\Storage;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
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
            trigger_deprecation('sylius/core-bundle', '2.0', sprintf('Passing an instance of %s as constructor argument for %s is deprecated as of Sylius 1.12 and will be removed in 2.0. Pass an instance of %s instead.', SessionInterface::class, self::class, RequestStack::class));
        }
    }

    public function hasForChannel(ChannelInterface $channel): bool
    {
        $session = $this->getSession();

        return $session->has($this->getCartKeyName($channel));
    }

    public function getForChannel(ChannelInterface $channel): ?OrderInterface
    {
        if ($this->hasForChannel($channel)) {
            $session = $this->getSession();

            $cartId = $session->get($this->getCartKeyName($channel));

            return $this->orderRepository->findCartByChannel($cartId, $channel);
        }

        return null;
    }

    public function setForChannel(ChannelInterface $channel, OrderInterface $cart): void
    {
        $session = $this->getSession();

        $session->set($this->getCartKeyName($channel), $cart->getId());
    }

    public function removeForChannel(ChannelInterface $channel): void
    {
        $session = $this->getSession();

        $session->remove($this->getCartKeyName($channel));
    }

    private function getCartKeyName(ChannelInterface $channel): string
    {
        return sprintf('%s.%s', $this->sessionKeyName, $channel->getCode());
    }

    private function getSession(): SessionInterface
    {
        if ($this->requestStackOrSession instanceof SessionInterface) {
            return $this->requestStackOrSession;
        }

        return $this->requestStackOrSession->getSession();
    }
}
