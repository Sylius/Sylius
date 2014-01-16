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
use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\RuntimeException;
use Payum\Core\Request\NotifyRequest;
use Sylius\Bundle\OrderBundle\Repository\OrderRepositoryInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class SecureNotifyAction extends PaymentAwareAction
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
            throw new RuntimeException('Hash cannot be verified.');
        }

        $order = $this->orderRepository->findOneBy(array($this->identifier => $details['ORDERID']));

        if (null === $order) {
            throw new RuntimeException('Order cannot be retrieved.');
        }

        $request->setModel($order);

        $this->payment->execute($request);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        if (!$request instanceof NotifyRequest) {
            return false;
        }

        $model = $request->getModel();

        return empty($model['ORDERID']);
    }
}
