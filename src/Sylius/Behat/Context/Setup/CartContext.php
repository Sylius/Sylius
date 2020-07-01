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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\PickupCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    /** @var MessageBusInterface */
    private $commandBus;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        MessageBusInterface $commandBus,
        OrderRepositoryInterface $orderRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $this->commandBus = $commandBus;
        $this->orderRepository = $orderRepository;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given I added product :product to the cart
     */
    public function iAddedProductToTheCart(ProductInterface $product): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findLatestCart();

        if ($order === null) {
            $this->commandBus->dispatch(new PickupCart());

            /** @var OrderInterface|null $order */
            $order = $this->orderRepository->findLatestCart();
        }

        Assert::notNull($order);

        $cartToken = $order->getTokenValue();

        $this->commandBus->dispatch(AddItemToCart::createFromData(
            $cartToken,
            $product->getCode(),
            1
        ));

        $this->sharedStorage->set('cart_token', $cartToken);
    }
}
