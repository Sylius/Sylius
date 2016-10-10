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

use Sylius\Component\Customer\Model\CustomerGroupInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GroupBasedCalculator extends AbstractCalculator
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::GROUP_BASED;
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameterName()
    {
        return 'groups';
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassName()
    {
        return CustomerGroupInterface::class;
    }
}
