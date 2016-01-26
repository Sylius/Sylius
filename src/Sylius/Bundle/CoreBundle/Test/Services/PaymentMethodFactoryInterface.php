<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Test\Services;

use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface PaymentMethodFactoryInterface extends FactoryInterface
{
    /**
     * @param array $parameters
     *
     * @return PaymentMethodInterface
     */
    public function createFromArray(array $parameters);
}
