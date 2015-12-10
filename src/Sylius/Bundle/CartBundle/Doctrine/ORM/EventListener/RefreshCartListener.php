<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Doctrine\ORM\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Sylius\Component\Cart\Model\CartInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class RefreshCartListener
{
    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof CartInterface) {
                $this->clearAdjustmentsOnEmptyCart($entity);
                $entity->calculateTotal();
            }
        }
    }

    /**
     * @param CartInterface $cart
     */
    private function clearAdjustmentsOnEmptyCart(CartInterface $cart)
    {
        if ($cart->isEmpty()) {
            $cart->clearAdjustments();
        }
    }
}
