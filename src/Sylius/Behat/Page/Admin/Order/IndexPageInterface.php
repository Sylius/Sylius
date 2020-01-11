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

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function specifyFilterDateFrom(string $dateTime): void;

    public function specifyFilterDateTo(string $dateTime): void;

    public function chooseChannelFilter(string $channelName): void;

    public function chooseCurrencyFilter(string $currencyName): void;

    public function specifyFilterTotalGreaterThan(string $total): void;

    public function specifyFilterTotalLessThan(string $total): void;
}
