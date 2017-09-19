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

namespace Sylius\Component\Core\Model;

use Sylius\Component\Customer\Model\CustomerGroupInterface as BaseCustomerGroupInterface;

interface CustomerGroupInterface extends BaseCustomerGroupInterface
{
    /**
     * @return CustomerTaxCategoryInterface|null
     */
    public function getTaxCategory(): ?CustomerTaxCategoryInterface;

    /**
     * @param CustomerTaxCategoryInterface|null $taxCategory
     */
    public function setTaxCategory(?CustomerTaxCategoryInterface $taxCategory): void;
}
