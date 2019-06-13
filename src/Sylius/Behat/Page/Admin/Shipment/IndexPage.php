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

namespace Sylius\Behat\Page\Admin\Shipment;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function chooseStateToFilter(string $shipmentState): void
    {
        $this->getElement('filter_state')->selectOption($shipmentState);
    }

    public function shipShipmentOfOrderWithNumber(string $orderNumber): void
    {
        $this->getField($orderNumber, 'actions')->pressButton('Ship');
    }

    public function getShipmentStatusByOrderNumber(string $orderNumber): string
    {
        return $this->getField($orderNumber, 'state')->getText();
    }

    private function getField(string $orderNumber, string $fieldName): NodeElement
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['number' => $orderNumber]);

        return $tableAccessor->getFieldFromRow($table, $row, $fieldName);
    }

    public function showOrderPageForNthShipment(int $shipmentNumber): void
    {
        $this->getActionsForRow($shipmentNumber)->clickLink('Show order details');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_state' => '#criteria_state',
        ]);
    }

    private function getActionsForRow(int $shipmentNumber): NodeElement
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowsWithFields($table, [])[$shipmentNumber];

        return $tableAccessor->getFieldFromRow($table, $row, 'actions');
    }
}
