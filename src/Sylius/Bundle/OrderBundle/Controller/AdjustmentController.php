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
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        /** @var $order OrderInterface */
        if (!$order = $this->container->get('sylius.repository.order')->findOneById($id)) {
            throw new NotFoundHttpException('Requested order does not exist.');
        }

        if (!$request->query->has('adjustment')) {
            $adjustments = $order->getAdjustments();
        } else {
            $id = $request->query->get('adjustment');
            $adjustments = $order->getAdjustments()->filter(function (AdjustmentInterface $adjustment) use ($id) {
                return $id == $adjustment->getId();
            });
        }

        /** @var $adjustments AdjustmentInterface[] */
        foreach ($adjustments as $adjustment) {
            if ($request->get('lock', false)) {
                $adjustment->lock();
            } else {
                $adjustment->unlock();
            }
        }

        $this->manager->flush();

        return $this->redirectHandler->redirectToResource($configuration, $order);
    }
}
