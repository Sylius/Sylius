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
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Payment\Calculator\DelegatingFeeCalculatorInterface;
use Sylius\Component\Payment\Model\PaymentSubjectInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
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
     * @var OriginatorInterface
     */
    protected $originator;

    /**
     * @param RepositoryInterface              $adjustmentRepository
     * @param DelegatingFeeCalculatorInterface $feeCalculator
     * @param OriginatorInterface              $originator
     */
    public function __construct(
        RepositoryInterface $adjustmentRepository,
        DelegatingFeeCalculatorInterface $feeCalculator,
        OriginatorInterface $originator
    ) {
        $this->adjustmentRepository = $adjustmentRepository;
        $this->feeCalculator = $feeCalculator;
        $this->originator = $originator;
    }

    /**
     * @param OrderInterface $order
     */
    public function applyPaymentCharges(OrderInterface $order)
    {
        foreach ($order->getPayments() as $payment) {
            if (PaymentInterface::STATE_CANCELLED === $payment->getState()) {
                continue;
            }

            foreach ($order->getAdjustments(AdjustmentInterface::PROMOTION_ADJUSTMENT) as $adjustment) {
                if ($payment === $this->originator->getOrigin($adjustment)) {
                    $order->removeAdjustment($adjustment);
                }
            }

            $order->addAdjustment($this->createAdjustment($payment));
        }

        $order->calculateTotal();
    }

    /**
     * @param PaymentSubjectInterface $payment
     *
     * @return AdjustmentInterface
     */
    private function createAdjustment(PaymentSubjectInterface $payment)
    {
        $adjustment = $this->adjustmentRepository->createNew();
        $adjustment->setLabel(AdjustmentInterface::PAYMENT_ADJUSTMENT);
        $adjustment->setAmount($this->feeCalculator->calculate($payment));
        $adjustment->setDescription($payment->getMethod()->getName());

        $this->originator->setOrigin($adjustment, $payment);

        return $adjustment;
    }
}
