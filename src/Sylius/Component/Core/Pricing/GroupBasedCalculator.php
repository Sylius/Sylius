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
 * Customer group based calculator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GroupBasedCalculator extends AbstractCalculator implements CalculatorInterface
{
    protected $parameterName = 'groups';
    protected $className     = 'Sylius\Component\User\Model\GroupInterface';

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::GROUP_BASED;
    }
}
