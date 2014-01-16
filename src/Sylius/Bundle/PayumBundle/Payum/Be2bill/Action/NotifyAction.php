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

use Payum\Be2Bill\Api;
use Payum\Bundle\PayumBundle\Request\ResponseInteractiveRequest;
use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sylius\Bundle\OrderBundle\Repository\OrderRepositoryInterface;
use Sylius\Bundle\PaymentsBundle\SyliusPaymentEvents;
use Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class NotifyAction extends PaymentAwareAction
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    public function __construct(Api $api, OrderRepositoryInterface $orderRepository, $identifier)
    {
        $this->api = $api;
        $this->orderRepository = $orderRepository;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request NotifyRequest */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        $details = $request->getModel();

        if (!$this->api->verifyHash($details)) {
            throw new BadRequestHttpException('Hash cannot be verified.');
        }

        if (empty($details['ORDERID'])) {
            throw new BadRequestHttpException('Order id cannot be guessed');
        }

        $order = $this->orderRepository->findOneBy(array($this->identifier => $details['ORDERID']));

        if (null === $order) {
            throw new BadRequestHttpException('Order cannot be retrieved.');
        }

        $previousState = $order->getPayment()->getState();

        $status = new StatusRequest($order);
        $this->payment->execute($status);

        $order->getPayment()->setState($status->getStatus());

        if ($previousState !== $order->getPayment()->getState()) {
            $this->eventDispatcher->dispatch(
                SyliusPaymentEvents::PRE_STATE_CHANGE,
                new GenericEvent($order->getPayment(), array('previous_state' => $previousState))
            );

            $this->eventDispatcher->dispatch(
                SyliusPaymentEvents::POST_STATE_CHANGE,
                new GenericEvent($order->getPayment(), array('previous_state' => $previousState))
            );
        }

        throw new ResponseInteractiveRequest(new Response('OK', 200));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof NotifyRequest;
    }
}
