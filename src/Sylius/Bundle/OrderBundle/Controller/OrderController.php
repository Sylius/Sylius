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

/**
 * Order controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderController extends ResourceController
{
    /**
     * Confirms order.
     *
     * @param string $confirmationToken
     */
    public function confirmAction($confirmationToken)
    {
        $order = $this->findOr404(array('confirmationToken' => $confirmationToken));

        $order->setConfirmed(true);
        $this->persistAndFlush($order);

        return $this->renderResponse('confirmed.html', array(
            'order' => $order
        ));
    }
}
