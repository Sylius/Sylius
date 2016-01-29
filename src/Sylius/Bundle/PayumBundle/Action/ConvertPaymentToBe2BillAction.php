<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Action;

use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;
use Payum\Core\Request\GetHttpRequest;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ConvertPaymentToBe2BillAction extends GatewayAwareAction
{
    /**
     * {@inheritdoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();
        $order = $payment->getOrder();

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        $details = [];
        $details['AMOUNT'] = $order->getTotal();
        $details['CLIENTEMAIL'] = $order->getCustomer()->getEmail();
        $details['CLIENTUSERAGENT'] = $httpRequest->userAgent ?: 'Unknown';
        $details['CLIENTIP'] = $httpRequest->clientIp;
        $details['CLIENTIDENT'] = $order->getCustomer()->getId();
        $details['DESCRIPTION'] = sprintf('Order containing %d items for a total of %01.2f', $order->getItems()->count(), $order->getTotal() / 100);
        $details['ORDERID'] = $payment->getId();

        $request->setResult($details);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() === 'array'
        ;
    }
}
