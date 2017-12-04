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
use Sylius\Component\Core\Storage\CartStorageInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;

final class SessionAndChannelBasedCartContext implements CartContextInterface
{
    /**
     * @var CartStorageInterface
     */
    private $cartStorage;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param CartStorageInterface $cartStorage
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(CartStorageInterface $cartStorage, ChannelContextInterface $channelContext)
    {
        $this->cartStorage = $cartStorage;
        $this->channelContext = $channelContext;
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

        if (!$this->cartStorage->hasForChannel($channel)) {
            throw new CartNotFoundException('Sylius was not able to find the cart in session');
        }

        $cart = $this->cartStorage->getForChannel($channel);
        if (null === $cart) {
            $this->cartStorage->removeForChannel($channel);

            throw new CartNotFoundException('Sylius was not able to find the cart in session');
        }

        return $cart;
    }
}
