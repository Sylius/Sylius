<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Pagerfanta\Pagerfanta;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface PaymentRepositoryInterface extends RepositoryInterface
{
    /**
     * @param mixed $orderId
     * @param mixed $id
     *
     * @return PaymentInterface|null
     */
    public function findByOrderIdAndId($orderId, $id);
}
