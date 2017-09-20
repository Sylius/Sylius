<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface CustomerTaxCategoryProviderInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return CustomerTaxCategoryInterface|null
     */
    public function getCustomerTaxCategory(OrderInterface $order): ?CustomerTaxCategoryInterface;
}
