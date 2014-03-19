<?php

/*
 * This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Releaser;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;
use Sylius\Bundle\ShippingBundle\Model\ShipmentInterface;
use Sylius\Bundle\ShippingBundle\Processor\ShipmentProcessorInterface;

/**
 * Release expired orders.
 *
 * @author Foo Pang <foo.pang@gmail.com>
 */
class ExpiredOrdersReleaser implements ReleaserInterface
{
    /**
     * Shipping processor.
     *
     * @var ShipmentProcessorInterface
     */
    protected $shippingProcessor;

    public function __construct(ShipmentProcessorInterface $shippingProcessor)
    {
        $this->shippingProcessor = $shippingProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function release(OrderInterface $order)
    {
        $order->setPaymentState(PaymentInterface::STATE_VOID);
        $order->getPayment()->setState($order->getPaymentState());
        $order->setShippingState(ShipmentInterface::STATE_CHECKOUT);
        $this->shippingProcessor->updateShipmentStates($order->getShipments(), $order->getShippingState());
    }
}
