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

namespace Sylius\Behat\Page\Shop\Account\Order;

use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\RouterInterface;

class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * @var TableAccessorInterface
     */
    private $tableAccessor;

    /**
     * @param Session $session
     * @param array $parameters
     * @param RouterInterface $router
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
    public function getRouteName()
    {
        return 'sylius_shop_account_order_index';
    }

    /**
     * {@inheritdoc}
     */
    public function countOrders()
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('customer_orders'));
    }

    public function openLastOrderPage()
    {
        $this->getElement('last_order')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function isOrderWithNumberInTheList($number)
    {
        try {
            $rows = $this->tableAccessor->getRowsWithFields(
                $this->getElement('customer_orders'),
                ['number' => $number]
            );

            return 1 === count($rows);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isItPossibleToChangePaymentMethodForOrder(OrderInterface $order)
    {
        $row = $this->tableAccessor->getRowWithFields(
            $this->getElement('customer_orders'),
            ['number' => $order->getNumber()]
        );

        return $row->hasLink('Pay');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'customer_orders' => 'table',
            'last_order' => 'table tbody tr:last-child a:contains("Show")',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function isCancelButtonVisibleForOrderWithNumber($number)
    {
        $orderData = $this->getSession()->getPage()->find('css', sprintf('tr:contains("%s")', $number));

        if (null === $orderData) {
            return false;
        }

        $actionButtonsText = $orderData->find('css', 'td:last-child')->getText();

        if (!strpos($actionButtonsText, 'Cancel')) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clickCancelButtonNextToTheOrder($number)
    {
        $orderData = $this->getSession()->getPage()->find('css', sprintf('tr:contains("%s")', $number));

        if (null === $orderData) {
            throw new \Exception(sprintf('There is no order %s on the orders list', $number));
        }

        $cancelButton = $orderData->find('css', 'td:last-child')->find('css', 'button');

        if (null === $cancelButton || $cancelButton->getText() != 'Cancel') {
            throw new \Exception(sprintf('There is no cancel button next to order %s', $number));
        }

        $cancelButton->click();
    }

    /**
     * {@inheritdoc}
     */
    public function theOrderShouldBeCancelled($number)
    {
        $orderData = $this->getSession()->getPage()->find('css', sprintf('tr:contains("%s")', $number));

        if (null === $orderData) {
            throw new \Exception(sprintf('There is no order %s on the orders list', $number));
        }

        if ($orderData->find('css', 'td:nth-child(5)')->getText() !== 'Cancelled') {
            throw new \Exception(sprintf('There order %s is not cancelled', $number));
        }
    }
}
