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

use Doctrine\Common\Persistence\ObjectManager;
use Finite\Factory\FactoryInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\SecuredNotifyRequest;
use Payum\Core\Request\SyncRequest;
use Sylius\Bundle\PayumBundle\Payum\Action\AbstractPaymentStateAwareAction;
use Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotifyOrderAction extends AbstractPaymentStateAwareAction
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param ObjectManager            $objectManager
     * @param FactoryInterface         $factory
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, ObjectManager $objectManager, FactoryInterface $factory)
    {
        parent::__construct($factory);

        $this->eventDispatcher = $eventDispatcher;
        $this->objectManager   = $objectManager;
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

        /** @var $payment PaymentInterface */
        $payment = $request->getModel();

        $this->payment->execute(new SyncRequest($payment));

        $status = new StatusRequest($payment);
        $this->payment->execute($status);

        $nextState = $status->getStatus();

        $this->updatePaymentState($payment, $nextState);

        $this->objectManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof SecuredNotifyRequest &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}
