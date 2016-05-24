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

use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DashboardPage extends SymfonyPage implements DashboardPageInterface
{
    /**
     * @var TableAccessorInterface
     */
    private $tableAccessor;

    /**
     * @param TableAccessorInterface $tableAccessor
     */
    public function __construct(
        Session $session,
        array $parameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor
    ) {
        parent::__construct($session, $parameters, $router);

        $this->tableAccessor = $tableAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalSales()
    {
        return $this->getElement('total_sales')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberOfNewOrders()
    {
        return (int) $this->getElement('new_orders')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberOfNewOrdersInTheList()
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('order_list'));
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberOfNewCustomers()
    {
        return (int) $this->getElement('new_customers')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberOfNewCustomersInTheList()
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('customer_list'));
    }

    /**
     * {@inheritdoc}
     */
    public function getAverageOrderValue()
    {
        return $this->getElement('average_order_value')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_admin_dashboard';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'total_sales' => '#total-sales',
            'new_orders' => '#new-orders',
            'new_customers' => '#new-customers',
            'average_order_value' => '#average-order-value',
            'customer_list' => '#customers',
            'order_list' => '#orders',
        ]);
    }
}
