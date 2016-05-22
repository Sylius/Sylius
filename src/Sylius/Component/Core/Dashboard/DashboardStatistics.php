<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Dashboard;

use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DashboardStatistics
{
    /**
     * @var int
     */
    private $totalSales;

    /**
     * @var int
     */
    private $numberOfNewOrders;

    /**
     * @var int
     */
    private $numberOfNewCustomers;

    /**
     * @var int
     */
    private $averageOrderValue;

    /**
     * @param int $totalSales
     * @param int $numberOfNewOrders
     * @param int $numberOfNewCustomers
     */
    public function __construct($totalSales, $numberOfNewOrders, $numberOfNewCustomers)
    {
        Assert::allInteger([$totalSales, $numberOfNewCustomers, $numberOfNewOrders]);

        $this->totalSales = $totalSales;
        $this->numberOfNewOrders = $numberOfNewOrders;
        $this->numberOfNewCustomers = $numberOfNewCustomers;
    }

    /**
     * @return int
     */
    public function getTotalSales()
    {
        return $this->totalSales;
    }

    /**
     * @return int
     */
    public function getNumberOfNewOrders()
    {
        return $this->numberOfNewOrders;
    }

    /**
     * @return int
     */
    public function getNumberOfNewCustomers()
    {
        return $this->numberOfNewCustomers;
    }

    /**
     * @return int
     */
    public function getAverageOrderValue()
    {
        return (int) round($this->totalSales / $this->numberOfNewOrders);
    }
}
