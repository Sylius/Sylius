<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Extension;

use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Model\ModelAggregateInterface;
use Payum\Core\Storage\IdentityInterface;
use Payum\Core\Storage\StorageInterface;
use SM\Factory\FactoryInterface;
use SM\SMException;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Webmozart\Assert\Assert;

/**
 * Reproduction of the Payum Core StorageExtension behaviour to apply Sylius payment state machine
 * at the very end of a Payum request
 *
 * @see \Payum\Core\Extension\StorageExtension
 */
final class UpdatePaymentStateExtension implements ExtensionInterface
{
    /** @var PaymentInterface[] */
    private array $scheduledPaymentsToProcess = [];

    public function __construct(
        private FactoryInterface $factory,
        private StorageInterface $storage,
        private GetStatusFactoryInterface $getStatusRequestFactory
    ) {
    }

    public function onPreExecute(Context $context): void
    {
        /** @var mixed|ModelAggregateInterface $request */
        $request = $context->getRequest();

        if (false === $request instanceof ModelAggregateInterface) {
            return;
        }

        if ($request->getModel() instanceof IdentityInterface) {
            $payment = $this->storage->find($request->getModel());
        } else {
            /** @var PaymentInterface|mixed $payment */
            $payment = $request->getModel();
        }

        if (false === $payment instanceof PaymentInterface) {
            return;
        }

        $this->scheduleForProcessingIfSupported($payment);
    }

    public function onExecute(Context $context): void
    {
    }

    public function onPostExecute(Context $context): void
    {
        if (null !== $context->getException()) {
            return;
        }

        /** @var mixed|ModelAggregateInterface $request */
        $request = $context->getRequest();

        if ($request instanceof ModelAggregateInterface) {
            /** @var PaymentInterface|mixed $payment */
            $payment = $request->getModel();
            if ($payment instanceof PaymentInterface) {
                $this->scheduleForProcessingIfSupported($payment);
            }
        }

        if (count($context->getPrevious()) > 0) {
            return;
        }

        foreach ($this->scheduledPaymentsToProcess as $id => $payment) {
            $this->processPayment($context, $payment);
            unset($this->scheduledPaymentsToProcess[$id]);
        }
    }

    /**
     * @throws SMException
     */
    private function processPayment(Context $context, PaymentInterface $payment): void
    {
        $status = $this->getStatusRequestFactory->createNewWithModel($payment);
        $context->getGateway()->execute($status);
        $value = (string) $status->getValue();
        if ($payment->getState() === $value) {
            return;
        }

        if (PaymentInterface::STATE_UNKNOWN === $value) {
            return;
        }

        $this->updatePaymentState($payment, $value);
    }

    private function updatePaymentState(PaymentInterface $payment, string $nextState): void
    {
        $stateMachine = $this->factory->get($payment, PaymentTransitions::GRAPH);
        Assert::isInstanceOf($stateMachine, StateMachineInterface::class);

        $transition = $stateMachine->getTransitionToState($nextState);
        if (null === $transition) {
            return;
        }

        $stateMachine->apply($transition);
    }

    private function scheduleForProcessingIfSupported(PaymentInterface $payment): void
    {
        /** @var int|null $id */
        $id = $payment->getId();
        if (null === $id) {
            return;
        }

        $this->scheduledPaymentsToProcess[$id] = $payment;
    }
}
