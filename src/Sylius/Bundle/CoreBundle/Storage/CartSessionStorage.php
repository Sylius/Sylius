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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartSessionStorage implements CartStorageInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $sessionKeyName;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param SessionInterface $session
     * @param string $sessionKeyName
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        SessionInterface $session,
        string $sessionKeyName,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->session = $session;
        $this->sessionKeyName = $sessionKeyName;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function hasForChannel(ChannelInterface $channel): bool
    {
        return $this->session->has($this->getCartKeyName($channel));
    }

    /**
     * {@inheritdoc}
     */
    public function getForChannel(ChannelInterface $channel): ?OrderInterface
    {
        if ($this->hasForChannel($channel)) {
            $cartId = $this->session->get($this->getCartKeyName($channel));

            return $this->orderRepository->findCartByChannel($cartId, $channel);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setForChannel(ChannelInterface $channel, OrderInterface $cart): void
    {
        $this->session->set($this->getCartKeyName($channel), $cart->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function removeForChannel(ChannelInterface $channel): void
    {
        $this->session->remove($this->getCartKeyName($channel));
    }

    /**
     * @param ChannelInterface $channel
     *
     * @return string
     */
    private function getCartKeyName(ChannelInterface $channel): string
    {
        return sprintf('%s.%s', $this->sessionKeyName, $channel->getCode());
    }
}
