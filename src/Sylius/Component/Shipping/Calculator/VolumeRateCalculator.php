<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Calculator;

use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Antonio Peric <antonio@locastic.com>
 */
class VolumeRateCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ShippingSubjectInterface $subject, array $configuration)
    {
        return (int) round($configuration['amount'] * ($subject->getShippingVolume() / $configuration['division']));
    }


    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'volume_rate';
    }
}
