<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface PaymentMethodFactoryInterface extends FactoryInterface
{
    /**
     * @param string $gateway
     *
     * @return PaymentMethodInterface
     */
    public function createWithGateway($gateway);
}
