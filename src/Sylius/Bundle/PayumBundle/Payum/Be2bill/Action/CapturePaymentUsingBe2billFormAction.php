<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Be2bill\Action;

use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\SecuredCaptureRequest;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CapturePaymentUsingBe2billFormAction extends PaymentAwareAction
{
    /**
     * @var Request
     */
    protected $httpRequest;

    /**
     * Define the Symfony Request
     *
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->httpRequest = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($request)
    {
        /** @var $request SecuredCaptureRequest */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        if (!$this->httpRequest) {
            throw new LogicException('The action can be run only when http request is set.');
        }

        /** @var $payment PaymentInterface */
        $payment = $request->getModel();
        $order = $payment->getOrder();

        $details = $payment->getDetails();

        if (empty($details)) {
            $details = array();
            $details['AMOUNT'] = $order->getTotal();
            $details['CLIENTEMAIL'] = $order->getUser()->getEmail();
            $details['HIDECLIENTEMAIL'] = 'yes';
            $details['CLIENTUSERAGENT'] = $this->httpRequest->headers->get('User-Agent', 'Unknown');
            $details['CLIENTIP'] = $this->httpRequest->getClientIp();
            $details['CLIENTIDENT'] = $order->getUser()->getId();
            $details['DESCRIPTION'] = sprintf('Order containing %d items for a total of %01.2f', $order->getItems()->count(), $order->getTotal() / 100);
            $details['ORDERID'] = $payment->getId();

            $payment->setDetails((array) $details);
        }

        $details = ArrayObject::ensureArrayObject($details);

        try {
            $this->httpRequest->getSession()->set('payum_token', $request->getToken()->getHash());

            $request->setModel($details);
            $this->payment->execute($request);

            $payment->setDetails((array) $details);
            $request->setModel($payment);
        } catch (\Exception $e) {
            $payment->setDetails((array) $details);
            $request->setModel($payment);

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof SecuredCaptureRequest &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}
