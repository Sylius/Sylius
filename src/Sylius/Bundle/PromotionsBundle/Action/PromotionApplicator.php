<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Action;

use Sylius\Bundle\PromotionsBundle\Action\Registry\PromotionActionRegistryInterface;
use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;

/**
 * Applies all registered promotion actions to given order.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionApplicator implements PromotionApplicatorInterface
{
    protected $registry;

    public function __construct(PromotionActionRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function apply(OrderInterface $order, PromotionInterface $promotion)
    {
        foreach ($promotion->getActions() as $action) {
            $this->registry
                ->getAction($action->getType())
                ->execute($order, $action->getConfiguration())
            ;
        }
    }
}
