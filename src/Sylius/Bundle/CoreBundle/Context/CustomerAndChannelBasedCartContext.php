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
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CustomerAndChannelBasedCartContext implements CartContextInterface
{
    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param CustomerContextInterface $customerContext
     * @param ChannelContextInterface $channelContext
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        CustomerContextInterface $customerContext,
        ChannelContextInterface $channelContext,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->customerContext = $customerContext;
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
            throw new CartNotFoundException('Sylius was not able to find the cart, as there is no current channel.');
        }

        $customer = $this->customerContext->getCustomer();
        if (null === $customer) {
            throw new CartNotFoundException('Sylius was not able to find the cart, as there is no logged in user.');
        }

        $cart = $this->orderRepository->findLatestCartByChannelAndCustomer($channel, $customer);
        if (null === $cart) {
            throw new CartNotFoundException('Sylius was not able to find the cart for currently logged in user.');
        }

        return $cart;
    }
}
