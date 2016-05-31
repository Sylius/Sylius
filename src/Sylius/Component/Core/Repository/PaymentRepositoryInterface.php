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
use Sylius\Component\Payment\Repository\PaymentRepositoryInterface as BasePaymentRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface PaymentRepositoryInterface extends BasePaymentRepositoryInterface
{
    /**
     * @param array|null $criteria
     * @param array|null $sorting
     *
     * @return Pagerfanta
     */
    public function createFilterPaginator(array $criteria = null, array $sorting = null);
}
