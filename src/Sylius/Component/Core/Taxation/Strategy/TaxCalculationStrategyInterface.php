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

namespace Sylius\Component\Core\Taxation\Strategy;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
interface TaxCalculationStrategyInterface
{
    /**
     * @param OrderInterface $order
     * @param ZoneInterface $zone
     * @param CustomerTaxCategoryInterface $customerTaxCategory
     */
    public function applyTaxes(
        OrderInterface $order,
        ZoneInterface $zone,
        CustomerTaxCategoryInterface $customerTaxCategory
    ): void;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param OrderInterface $order
     * @param ZoneInterface $zone
     * @param CustomerTaxCategoryInterface $customerTaxCategory
     *
     * @return bool
     */
    public function supports(
        OrderInterface $order,
        ZoneInterface $zone,
        CustomerTaxCategoryInterface $customerTaxCategory
    ): bool;
}
