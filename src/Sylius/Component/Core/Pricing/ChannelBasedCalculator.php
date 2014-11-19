<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Pricing;

use Sylius\Component\Pricing\Calculator\CalculatorInterface;

/**
 * Channel based calculator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ChannelBasedCalculator extends AbstractCalculator implements CalculatorInterface
{
    protected $parameterName = 'channel';
    protected $className     = 'Sylius\Component\Core\Model\ChannelInterface';

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::CHANNEL_BASED;
    }
}
