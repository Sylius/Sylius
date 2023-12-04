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

namespace Sylius\Behat\Page\Shop\Account\Order;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\RouterInterface;

class IndexPage extends SymfonyPage implements IndexPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private TableAccessorInterface $tableAccessor,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_account_order_index';
    }

    public function countOrders(): int
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('customer_orders'));
    }

    public function changePaymentMethod(OrderInterface $order): void
    {
        $row = $this->tableAccessor->getRowWithFields(
            $this->getElement('customer_orders'),
            ['number' => $order->getNumber()],
        );

        $link = $row->find('css', '[data-test-button="sylius.ui.pay"]');
        $link->click();
    }

    public function hasFlashMessage(string $message): bool
    {
        return str_contains($this->getElement('flash_message')->getText(), $message);
    }

    public function isOrderWithNumberInTheList($number): bool
    {
        try {
            $rows = $this->tableAccessor->getRowsWithFields(
                $this->getElement('customer_orders'),
                ['number' => $number],
            );

            return 1 === count($rows);
        } catch (\InvalidArgumentException) {
            return false;
        }
    }

    public function openLastOrderPage(): void
    {
        $this->getElement('last_order')->find('css', '[data-test-button="sylius.ui.show"]')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'customer_orders' => '[data-test-grid-table]',
            'flash_message' => '[data-test-flash-message]',
            'last_order' => '[data-test-grid-table-body] [data-test-row]:last-child [data-test-actions]',
        ]);
    }
}
