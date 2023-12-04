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

namespace Sylius\Bundle\CoreBundle\Order\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPromotionsIntegrityChecker implements OrderPromotionsIntegrityCheckerInterface
{
    public function __construct(private OrderProcessorInterface $orderProcessor)
    {
    }

    public function check(OrderInterface $order): ?PromotionInterface
    {
        /** @var PromotionInterface[]|ArrayCollection $previousPromotions */
        $previousPromotions = new ArrayCollection($order->getPromotions()->toArray());

        $this->orderProcessor->process($order);

        /** @var PromotionInterface $previousPromotion */
        foreach ($previousPromotions as $previousPromotion) {
            if (!$order->getPromotions()->contains($previousPromotion)) {
                return $previousPromotion;
            }
        }

        return null;
    }
}
