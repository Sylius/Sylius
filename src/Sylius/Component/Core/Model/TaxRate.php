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

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Taxation\Model\TaxRate as BaseTaxRate;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxRate extends BaseTaxRate implements TaxRateInterface
{
    /**
     * @var ZoneInterface
     */
    protected $zone;

    /**
     * @var CustomerTaxCategoryInterface
     */
    protected $customerTaxCategory;

    /**
     * {@inheritdoc}
     */
    public function getZone(): ?ZoneInterface
    {
        return $this->zone;
    }

    /**
     * {@inheritdoc}
     */
    public function setZone(?ZoneInterface $zone): void
    {
        $this->zone = $zone;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerTaxCategory(): ?CustomerTaxCategoryInterface
    {
        return $this->customerTaxCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerTaxCategory(?CustomerTaxCategoryInterface $customerTaxCategory): void
    {
        $this->customerTaxCategory = $customerTaxCategory;
    }
}
