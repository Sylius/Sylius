<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Bundle\CoreBundle\Event\AdjustmentEvent;
use Sylius\Bundle\CoreBundle\EventListener\AdjustmentSubscriber;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\Model\AdjustmentDTO;
use Sylius\Component\Payment\Calculator\DelegatingFeeCalculatorInterface;
use Sylius\Component\Payment\Model\PaymentSubjectInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class PaymentChargesProcessor implements PaymentChargesProcessorInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var DelegatingFeeCalculatorInterface
     */
    protected $feeCalculator;

    /**
     * @param EventDispatcherInterface         $eventDispatcher
     * @param DelegatingFeeCalculatorInterface $feeCalculator
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DelegatingFeeCalculatorInterface $feeCalculator
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->feeCalculator = $feeCalculator;
    }

    /**
     * @param OrderInterface $order
     */
    public function applyPaymentCharges(OrderInterface $order)
    {
        $order->removeAdjustments(AdjustmentInterface::PAYMENT_ADJUSTMENT);
        $order->calculateTotal();

        foreach ($order->getPayments() as $payment) {
            $this->addAdjustmentIfForNotCancelled($order, $payment);
        }
    }

    /**
     * @param OrderInterface $order
     * @param PaymentSubjectInterface $payment
     */
    private function addAdjustmentIfForNotCancelled(OrderInterface $order, PaymentSubjectInterface $payment)
    {
        if (PaymentInterface::STATE_CANCELLED !== $payment->getState())
        {
            $adjustmentDTO = $this->getAdjustmentDTO($payment);

            $this->eventDispatcher->dispatch(
                AdjustmentEvent::ADJUSTMENT_ADDING_ORDER,
                new AdjustmentEvent(
                    $order,
                    [AdjustmentSubscriber::EVENT_ARGUMENT_DATA_KEY => $adjustmentDTO]
                )
            );
        }
    }

    /**
     * @param PaymentSubjectInterface $payment
     *
     * @return AdjustmentDTO
     */
    private function getAdjustmentDTO(PaymentSubjectInterface $payment)
    {
        $adjustmentDTO = new AdjustmentDTO();
        $adjustmentDTO->type = AdjustmentInterface::PAYMENT_ADJUSTMENT;
        $adjustmentDTO->amount = $this->feeCalculator->calculate($payment);
        $adjustmentDTO->originType = get_class($payment);
        $adjustmentDTO->description = $payment->getMethod()->getName();

        return $adjustmentDTO;
    }
}
