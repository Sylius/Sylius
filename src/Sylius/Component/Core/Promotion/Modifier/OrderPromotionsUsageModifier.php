<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Modifier;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderPromotionsUsageModifier implements OrderPromotionsUsageModifierInterface
{
    /**
     * @var ObjectManager
     */
    private $promotionManager;

    /**
     * @param ObjectManager $promotionManager
     */
    public function __construct(ObjectManager $promotionManager)
    {
        $this->promotionManager = $promotionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function increment(OrderInterface $order)
    {
        foreach ($order->getPromotions() as $promotion) {
            $promotion->incrementUsed();
        }

        $this->promotionManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function decrement(OrderInterface $order)
    {
        foreach ($order->getPromotions() as $promotion) {
            $promotion->decrementUsed();
        }

        $this->promotionManager->flush();
    }
}
