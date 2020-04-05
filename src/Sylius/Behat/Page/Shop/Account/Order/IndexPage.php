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
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\RouterInterface;

class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /** @var TableAccessorInterface */
    private $tableAccessor;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor
    ) {
        parent::__construct($session, $minkParameters, $router);

        $this->tableAccessor = $tableAccessor;
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_account_order_index';
    }

    public function countOrders(): int
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('customer_orders'));
    }

    public function isOrderWithNumberInTheList($number): bool
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

    public function isItPossibleToChangePaymentMethodForOrder(OrderInterface $order): bool
    {
        $row = $this->tableAccessor->getRowWithFields(
            $this->getElement('customer_orders'),
            ['number' => $order->getNumber()]
        );

        return $row->has('css', '[data-test-button="sylius.ui.pay"]');
    }

    public function openLastOrderPage(): void
    {
        $this->getElement('last_order')->find('css', '[data-test-button="sylius.ui.show"]')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'customer_orders' => '[data-test-grid-table]',
            'last_order' => '[data-test-grid-table-body] [data-test-row]:last-child [data-test-actions]',
        ]);
    }
}
