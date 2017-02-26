<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class SessionAndChannelBasedCartContext implements CartContextInterface
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
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param SessionInterface $session
     * @param string $sessionKeyName
     * @param ChannelContextInterface $channelContext
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        SessionInterface $session,
        $sessionKeyName,
        ChannelContextInterface $channelContext,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->session = $session;
        $this->sessionKeyName = $sessionKeyName;
        $this->channelContext = $channelContext;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
            throw new CartNotFoundException($exception);
        }

        if (!$this->session->has(sprintf('%s.%s', $this->sessionKeyName, $channel->getCode()))) {
            throw new CartNotFoundException('Sylius was not able to find the cart in session');
        }

        $cart = $this->orderRepository->findCartByChannel(
            $this->session->get(sprintf('%s.%s', $this->sessionKeyName, $channel->getCode())),
            $channel
        );

        if (null === $cart) {
            $this->session->remove(sprintf('%s.%s', $this->sessionKeyName, $channel->getCode()));

            throw new CartNotFoundException('Sylius was not able to find the cart in session');
        }

        return $cart;
    }
}
