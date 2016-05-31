<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Repository;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface PaymentRepositoryInterface extends RepositoryInterface
{
    /**
     * @param mixed $orderId
     * @param mixed $id
     *
     * @return PaymentInterface
     */
    public function findByOrderAndId($orderId, $id);
}
