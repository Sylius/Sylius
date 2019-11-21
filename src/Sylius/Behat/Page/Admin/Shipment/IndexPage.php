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

    public function chooseChannelFilter(string $channelName): void
    {
        $this->getElement('filter_channel')->selectOption($channelName);
    }

    public function isShipmentWithOrderNumberInPosition(string $orderNumber, int $position): bool
    {
        $result = $this->getElement('shipment_in_given_position', [
                '%position%' => $position,
                '%orderNumber%' => $orderNumber,
            ]);

        return $result !== null;
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

    public function showOrderPageForNthShipment(int $position): void
    {
        $this->getOrderLinkForRow($position)->clickLink('#');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_channel' => '#criteria_channel',
            'filter_state' => '#criteria_state',
            'shipment_in_given_position' => 'table tbody tr:nth-child(%position%) td:contains("%orderNumber%")',
        ]);
    }

    private function getOrderLinkForRow(int $shipmentNumber): NodeElement
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowsWithFields($table, [])[$shipmentNumber];

        return $row->find('css', 'td:nth-child(2)');
    }
}
