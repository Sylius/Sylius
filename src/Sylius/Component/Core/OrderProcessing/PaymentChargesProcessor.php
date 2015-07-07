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

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Calculator\DelegatingFeeCalculatorInterface;
use Sylius\Component\Payment\Model\PaymentSubjectInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class PaymentChargesProcessor implements PaymentChargesProcessorInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $adjustmentRepository;

    /**
     * @var DelegatingFeeCalculatorInterface
     */
    protected $feeCalculator;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $adjustmentRepository
     * @param DelegatingFeeCalculatorInterface $feeCalculator
     */
    public function __construct(RepositoryInterface $adjustmentRepository, DelegatingFeeCalculatorInterface $feeCalculator)
    {
        $this->adjustmentRepository = $adjustmentRepository;
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
     * @param OrderInterface   $order
     * @param PaymentSubjectInterface $payment
     */
    private function addAdjustmentIfForNotCancelled(OrderInterface $order, PaymentSubjectInterface $payment)
    {
        if (PaymentInterface::STATE_CANCELLED !== $payment->getState())
        {
            $order->addAdjustment($this->prepareAdjustmentForOrder($payment));
        }
    }

    /**
     * @param PaymentSubjectInterface $payment
     *
     * @return AdjustmentInterface
     */
    private function prepareAdjustmentForOrder(PaymentSubjectInterface $payment)
    {
        $adjustment = $this->adjustmentRepository->createNew();
        $adjustment->setLabel(AdjustmentInterface::PAYMENT_ADJUSTMENT);
        $adjustment->setAmount($this->feeCalculator->calculate($payment));
        $adjustment->setDescription($payment->getMethod()->getName());

        return $adjustment;
    }
}
