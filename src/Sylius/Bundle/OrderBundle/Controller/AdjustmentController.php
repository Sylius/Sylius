<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Order adjustment controller.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class AdjustmentController extends ResourceController
{
    public function lockAction(Request $request, $id)
    {
        /** @var $order OrderInterface */
        if (!$order = $this->get('sylius.repository.order')->find($id)) {
            /** @var $order OrderItemInterface */
            if (!$order = $this->get('sylius.repository.order_item')->find($id)) {
                throw new NotFoundHttpException('Requested order does not exist.');
            }
        }

        if (!$request->query->has('adjustment')) {
            $adjustments = $order->getAdjustments();
        } else {
            $adjustmentId = $request->query->get('adjustment');
            $adjustments  = $order->getAdjustments()->filter(function (AdjustmentInterface $adjustment) use ($adjustmentId) {
                return $adjustmentId === $adjustment->getId();
            });
        }

        /** @var $adjustments AdjustmentInterface[] */
        $this->manageAdjustments($adjustments, $request->get('lock', false));

        if (!isset($adjustmentId)) {
            foreach ($order->getItems() as $item) {
                $this->manageAdjustments($item->getAdjustments(), $request->get('lock', false));
            }
        } else {
            foreach ($order->getItems() as $item) {
                $adjustments = $item->getAdjustments()->filter(function (AdjustmentInterface $adjustment) use ($adjustmentId) {
                    return $adjustmentId === $adjustment->getId();
                });

                $this->manageAdjustments($adjustments, $request->get('lock', false));
            }
        }

        $this->domainManager->update($order);

        return $this->redirectHandler->redirectTo($order);
    }

    private function manageAdjustments($adjustments, $lock = false)
    {
        foreach ($adjustments as $adjustment) {
            if ($lock) {
                $adjustment->lock();
            } else {
                $adjustment->unlock();
            }
        }
    }
}
