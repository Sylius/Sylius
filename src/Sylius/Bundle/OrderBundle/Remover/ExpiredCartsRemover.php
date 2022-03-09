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

namespace Sylius\Bundle\OrderBundle\Remover;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\OrderBundle\SyliusExpiredCartsEvents;
use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ExpiredCartsRemover implements ExpiredCartsRemoverInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ObjectManager $orderManager,
        private EventDispatcherInterface $eventDispatcher,
        private string $expirationPeriod
    ) {
    }

    public function remove(): void
    {
        $expiredCarts = $this->orderRepository->findCartsNotModifiedSince(new \DateTime('-' . $this->expirationPeriod));

        $this->eventDispatcher->dispatch(new GenericEvent($expiredCarts), SyliusExpiredCartsEvents::PRE_REMOVE);

        foreach ($expiredCarts as $expiredCart) {
            $this->orderManager->remove($expiredCart);
        }

        $this->orderManager->flush();

        $this->eventDispatcher->dispatch(new GenericEvent($expiredCarts), SyliusExpiredCartsEvents::POST_REMOVE);
    }
}
