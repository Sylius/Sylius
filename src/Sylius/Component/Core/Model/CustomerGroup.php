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

use Sylius\Component\Customer\Model\CustomerGroup as BaseCustomerGroup;

class CustomerGroup extends BaseCustomerGroup implements CustomerGroupInterface
{
    /**
     * @var CustomerTaxCategoryInterface
     */
    protected $taxCategory;

    /**
     * {@inheritdoc}
     */
    public function getTaxCategory(): ?CustomerTaxCategoryInterface
    {
        return $this->taxCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxCategory(?CustomerTaxCategoryInterface $taxCategory): void
    {
        $this->taxCategory = $taxCategory;
    }
}
