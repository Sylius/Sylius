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

namespace Sylius\Bundle\CoreBundle\Order\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

/** @experimental */
final class OrderPromotionsIntegrityChecker implements OrderPromotionsIntegrityCheckerInterface
{
    public function __construct(
        private OrderProcessorInterface $orderProcessor,
    ) {
    }

    public function check(OrderInterface $order): bool
    {
        $previousPromotions = new ArrayCollection($order->getPromotions()->toArray());

        $this->orderProcessor->process($order);

        foreach ($previousPromotions as $previousPromotion) {
            if (!$order->getPromotions()->contains($previousPromotion)) {
                return false;
            }
        }

        return true;
    }
}
