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

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * OrderItem controller.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderItemController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        if (null === $orderId = $this->getRequest()->get('orderId')) {
            throw new NotFoundHttpException('No order id given.');
        }

        if (!$order = $this->getOrderRepository()->find($orderId)) {
            throw new NotFoundHttpException('Requested order does not exist.');
        }

        $orderItem = parent::createNew();
        $orderItem->setOrder($order);

        return $orderItem;
    }

    /**
     * Get order repository.
     *
     * @return ObjectRepository
     */
    protected function getOrderRepository()
    {
        return $this->get('sylius.repository.order');
    }
}
