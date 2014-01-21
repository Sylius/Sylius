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
use Payum\Core\Request\NotifyRequest;
use Sylius\Bundle\OrderBundle\Repository\OrderRepositoryInterface;
use Sylius\Bundle\PaymentsBundle\SyliusPaymentEvents;
use Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $identifier;

    public function __construct(Api $api, OrderRepositoryInterface $orderRepository, EventDispatcher $eventDispatcher, $identifier)
    {
        $this->api             = $api;
        $this->orderRepository = $orderRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->identifier      = $identifier;
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

        $details = $request->getNotification();

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

        $payment = $order->getPayment();

        if ((int) $details['AMOUNT'] !== $payment->getAmount()) {
            throw new BadRequestHttpException('Request amount cannot be verified against payment amount.');
        }

        $previousState = $payment->getState();

        // Actually update payment details
        $details = array_merge($payment->getDetails(), $details);
        $payment->setDetails($details);

        $status = new StatusRequest($order);
        $this->payment->execute($status);

        $payment->setState($status->getStatus());

        if ($previousState !== $payment->getState()) {
            $this->eventDispatcher->dispatch(
                SyliusPaymentEvents::PRE_STATE_CHANGE,
                new GenericEvent($payment, array('previous_state' => $previousState))
            );

            $this->eventDispatcher->dispatch(
                SyliusPaymentEvents::POST_STATE_CHANGE,
                new GenericEvent($payment, array('previous_state' => $previousState))
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
