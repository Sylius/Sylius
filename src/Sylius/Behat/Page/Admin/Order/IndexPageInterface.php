<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function specifyFilterDateFrom(string $dateTime): void;

    public function specifyFilterDateTo(string $dateTime): void;

    public function specifyFilterChannel(string $channelName): void;

    public function specifyFilterShippingMethod(string $methodName): void;

    public function chooseFilterCurrency(string $currencyName): void;

    public function specifyFilterTotalGreaterThan(string $total): void;

    public function specifyFilterTotalLessThan(string $total): void;

    public function specifyFilterProduct(string $productName): void;

    public function specifyFilterVariant(string $variantName): void;

    public function specifyFilterCustomer(string $customerName): void;
}
