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

namespace Sylius\Bundle\CoreBundle\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class SessionAndChannelBasedCartContext implements CartContextInterface
{
    public function __construct(private CartStorageInterface $cartStorage, private ChannelContextInterface $channelContext)
    {
    }

    public function getCart(): OrderInterface
    {
        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
            throw new CartNotFoundException(null, $exception);
        }
        Assert::isInstanceOf($channel, ChannelInterface::class);

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
