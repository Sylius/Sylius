<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\User\Context;

use Sylius\User\Model\CustomerInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface CustomerContextInterface
{
    /**
     * @return CustomerInterface|null
     */
    public function getCustomer();
}
