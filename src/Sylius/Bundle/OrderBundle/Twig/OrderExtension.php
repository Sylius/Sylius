<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Twig;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;

class OrderExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sylius_order_quantity', array($this, 'countQuantity')),
            new \Twig_SimpleFunction('sylius_order_adjustments', array($this, 'getAdjustments')),
            new \Twig_SimpleFunction('sylius_order_adjustments_total', array($this, 'getAdjustmentsTotal')),
        );
    }

    /**
     * @param OrderInterface $order
     *
     * @return int
     */
    public function countQuantity(OrderInterface $order)
    {
        $quantity = 0;
        foreach ($order->getItems() as $item) {
            $quantity += $item->getQuantity();
        }

        return $quantity;
    }

    /**
     * @param OrderInterface $order
     * @param string         $type
     *
     * @return Collection|AdjustmentInterface[]
     */
    public function getAdjustments(OrderInterface $order, $type)
    {
        $adjustments = $order->getAdjustments($type)->toArray();
        foreach ($order->getItems() as $item) {
            foreach ($item->getAdjustments($type) as $adjustment) {
                if (isset($adjustments[$adjustment->getDescription()])) {
                    $adjustments[$adjustment->getDescription()]['amount'] += $adjustment->getAmount();
                } else {
                    $adjustments[$adjustment->getDescription()] = array(
                        'amount'      => $adjustment->getAmount(),
                        'description' => $adjustment->getDescription(),
                    );
                }
            }
        }

        return $adjustments;
    }

    /**
     * @param OrderInterface $order
     * @param string         $type
     *
     * @return int
     */
    public function getAdjustmentsTotal(OrderInterface $order, $type)
    {
        $total = $order->getAdjustmentsTotal($type);
        foreach ($order->getItems() as $item) {
            $total += $item->getAdjustmentsTotal($type);
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_order';
    }
}
