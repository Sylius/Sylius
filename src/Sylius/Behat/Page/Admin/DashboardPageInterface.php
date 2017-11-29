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

namespace Sylius\Behat\Page\Admin;

use Sylius\Behat\Page\SymfonyPageInterface;

interface DashboardPageInterface extends SymfonyPageInterface
{
    /**
     * @return int
     */
    public function getTotalSales(): int;

    /**
     * @return int
     */
    public function getNumberOfNewOrders(): int;

    /**
     * @return int
     */
    public function getNumberOfNewOrdersInTheList(): int;

    /**
     * @return int
     */
    public function getNumberOfNewCustomers(): int;

    /**
     * @return int
     */
    public function getNumberOfNewCustomersInTheList(): int;

    /**
     * @return int
     */
    public function getAverageOrderValue(): int;

    /**
     * @return string
     */
    public function getSubHeader(): string;

    public function logOut(): void;

    /**
     * @param string $channelName
     */
    public function chooseChannel(string $channelName): void;
}
