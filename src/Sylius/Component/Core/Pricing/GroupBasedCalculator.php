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
    public function __construct()
    {
        $this->parameterName = 'groups';
        $this->className = 'Sylius\Component\User\Model\GroupInterface';
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::GROUP_BASED;
    }
}
