<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Calculator;

use Sylius\Component\Payment\Model\PaymentSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class DelegatingFeeCalculator implements DelegatingFeeCalculatorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $serviceRegistry;

    /**
     * Constructor.
     *
     * @param ServiceRegistryInterface $serviceRegistry
     */
    public function __construct(ServiceRegistryInterface $serviceRegistry)
    {
        $this->serviceRegistry = $serviceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(PaymentSubjectInterface $payment)
    {
        if (null === $payment->getMethod()) {
            throw new \InvalidArgumentException("Cannot calculate fee for payment without payment method configured.");
        }

        /** @var FeeCalculatorInterface $feeCalculator */
        $feeCalculator = $this->serviceRegistry->get($payment->getMethod()->getFeeCalculator());

        return $feeCalculator->calculate($payment, $payment->getMethod()->getFeeCalculatorConfiguration());
    }
}
