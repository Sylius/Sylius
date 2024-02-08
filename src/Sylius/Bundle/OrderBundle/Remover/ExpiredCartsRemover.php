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
        private string $expirationPeriod,
        private int $batchSize = 100,
    ) {
    }

    public function remove(): void
    {
        while ([] !== $expiredCarts = $this->getBatch()) {
            foreach ($expiredCarts as $expiredCart) {
                $this->orderManager->remove($expiredCart);
            }

            $this->processDeletion($expiredCarts);
        }
    }

    private function getBatch(): array
    {
        $terminalDate = new \DateTime(sprintf('-%s', $this->expirationPeriod));

        return $this->orderRepository->findCartsNotModifiedSince($terminalDate, $this->batchSize);
    }

    private function processDeletion(array $deletedCarts): void
    {
        $this->eventDispatcher->dispatch(new GenericEvent($deletedCarts), SyliusExpiredCartsEvents::PRE_REMOVE);
        $this->orderManager->flush();
        $this->eventDispatcher->dispatch(new GenericEvent($deletedCarts), SyliusExpiredCartsEvents::POST_REMOVE);
        $this->orderManager->clear();
    }
}
