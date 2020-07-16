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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ApiBundle\Command\PickupCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CartContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var OrderRepositoryInterface */
    private $repository;

    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $repository,
        MessageBusInterface $commandBus
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->repository = $repository;
        $this->commandBus = $commandBus;
    }

    /**
     * @Transform /^(cart)$/
     */
    public function provideCartToken(): OrderInterface
    {
        if ($this->sharedStorage->has('cart')) {
            return $this->sharedStorage->get('cart');
        }

        $this->commandBus->dispatch(new PickupCart());

        /** @var OrderInterface|null $cart */
        $cart = $this->repository->findLatestCart();

        $this->sharedStorage->set('cart', $cart);

        return $cart;
    }
}
