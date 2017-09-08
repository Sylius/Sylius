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

namespace Sylius\Bundle\CoreBundle\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class SessionAndChannelBasedCartContext implements CartContextInterface
{
    /**
     * @var CartStorageInterface
     */
    private $cartSessionStorage;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param CartStorageInterface $cartSessionStorage
     * @param ChannelContextInterface $channelContext
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        CartStorageInterface $cartSessionStorage,
        ChannelContextInterface $channelContext,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->cartSessionStorage = $cartSessionStorage;
        $this->channelContext = $channelContext;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart(): OrderInterface
    {
        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
            throw new CartNotFoundException(null, $exception);
        }

        if (!$this->cartSessionStorage->hasCartId($channel->getCode())) {
            throw new CartNotFoundException('Sylius was not able to find the cart in session');
        }

        $cart = $this->orderRepository->findCartByChannel(
            $this->cartSessionStorage->getCartId($channel->getCode()),
            $channel
        );

        if (null === $cart) {
            $this->cartSessionStorage->removeCartId($channel->getCode());

            throw new CartNotFoundException('Sylius was not able to find the cart in session');
        }

        return $cart;
    }
}
