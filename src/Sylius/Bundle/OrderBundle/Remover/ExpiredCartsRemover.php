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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\OrderBundle\SyliusExpiredCartsEvents;
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
     * @var ObjectManager
     */
    private $orderManager;

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
     * @param ObjectManager $orderManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $expirationPeriod
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $orderManager,
        EventDispatcherInterface $eventDispatcher,
        $expirationPeriod
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderManager = $orderManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->expirationPeriod = $expirationPeriod;
    }

    public function remove()
    {
        $expiredCarts = $this->orderRepository->findCartsNotModifiedSince(new \DateTime('-'.$this->expirationPeriod));

        $this->eventDispatcher->dispatch(SyliusExpiredCartsEvents::PRE_REMOVE, new GenericEvent($expiredCarts));

        foreach ($expiredCarts as $expiredCart) {
            $this->orderManager->remove($expiredCart);
        }

        $this->orderManager->flush();

        $this->eventDispatcher->dispatch(SyliusExpiredCartsEvents::POST_REMOVE, new GenericEvent($expiredCarts));
    }
}
