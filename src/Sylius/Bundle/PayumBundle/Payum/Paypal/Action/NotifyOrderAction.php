<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Paypal\Action;

use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\SecuredNotifyRequest;
use Payum\Core\Request\SyncRequest;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PaymentsBundle\SyliusPaymentEvents;
use Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class NotifyOrderAction extends PaymentAwareAction
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request SecuredNotifyRequest */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /** @var OrderInterface $order */
        $order = $request->getModel();
        $payment = $order->getPayment();
        $previousState = $payment->getState();

        $this->payment->execute(new SyncRequest($payment));

        $status = new StatusRequest($payment);
        $this->payment->execute($status);
        $payment->setState($status->getStatus());

        if ($previousState !== $payment->getState()) {
            $this->eventDispatcher->dispatch(
                SyliusPaymentEvents::PRE_STATE_CHANGE,
                new GenericEvent($order->getPayment(), array('previous_state' => $previousState))
            );

            $this->eventDispatcher->dispatch(
                SyliusPaymentEvents::POST_STATE_CHANGE,
                new GenericEvent($order->getPayment(), array('previous_state' => $previousState))
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof SecuredNotifyRequest &&
            $request->getModel() instanceof OrderInterface
        ;
    }
}
