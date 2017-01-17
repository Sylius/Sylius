<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin;

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface DashboardPageInterface extends SymfonyPageInterface
{
    /**
     * @return int
     */
    public function getTotalSales();

    /**
     * @return int
     */
    public function getNumberOfNewOrders();

    /**
     * @return int
     */
    public function getNumberOfNewOrdersInTheList();

    /**
     * @return int
     */
    public function getNumberOfNewCustomers();

    /**
     * @return int
     */
    public function getNumberOfNewCustomersInTheList();

    /**
     * @return int
     */
    public function getAverageOrderValue();

    /**
     * @return string
     */
    public function getSubHeader();

    public function logOut();

    /**
     * @param string $channelName
     */
    public function chooseChannel($channelName);
}
