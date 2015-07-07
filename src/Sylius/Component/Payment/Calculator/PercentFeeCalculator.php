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

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class PercentFeeCalculator implements FeeCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(PaymentSubjectInterface $payment, array $configuration)
    {
        return (int) round(($configuration['percent'])/100 * $payment->getAmount());
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'percent';
    }
}
