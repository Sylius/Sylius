<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Remover;

use Sylius\Bundle\OrderBundle\SyliusCartsRemoveEvents;
use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ExpiredCartsRemover implements ExpiredCartsRemoverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var string
     */
    private $expirationPeriod;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $expirationPeriod
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        EventDispatcherInterface $eventDispatcher,
        $expirationPeriod
    ) {
        $this->orderRepository = $orderRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->expirationPeriod = $expirationPeriod;
    }

    public function remove()
    {
        $expiredCarts = $this->orderRepository->findCartsNotModifiedSince(new \DateTime('-'.$this->expirationPeriod));

        $this->eventDispatcher->dispatch(SyliusCartsRemoveEvents::CARTS_PRE_REMOVE, new GenericEvent($expiredCarts));

        foreach ($expiredCarts as $expiredCart) {
            $this->orderRepository->remove($expiredCart);
        }

        $this->eventDispatcher->dispatch(SyliusCartsRemoveEvents::CARTS_POST_REMOVE, new GenericEvent($expiredCarts));
    }
}
