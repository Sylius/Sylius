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

namespace Sylius\Behat\Page\Admin\Payment;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function completePaymentOfOrderWithNumber(string $orderNumber): void
    {
        $this->getField($orderNumber, 'actions')->pressButton('Complete');
    }

    public function chooseStateToFilter(string $paymentState): void
    {
        $this->getElement('filter_state')->selectOption($paymentState);
    }

    public function getPaymentStateByOrderNumber(string $orderNumber): string
    {
        return $this->getField($orderNumber, 'state')->getText();
    }

    public function isPaymentWithOrderNumberInPosition(string $orderNumber, int $position): bool
    {
        $result = $this->getElement('payment_in_given_position', [
            '%position%' => $position,
            '%orderNumber%' => $orderNumber,
        ]);

        return $result !== null;
    }

    public function showOrderPageForNthPayment(int $position): void
    {
        $this->getOrderLinkForRow($position)->clickLink('#');
    }

    public function chooseChannelFilter(string $channelName): void
    {
        $this->getElement('filter_channel')->selectOption($channelName);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_channel' => '#criteria_channel',
            'filter_state' => '#criteria_state',
            'payment_in_given_position' => 'table tbody tr:nth-child(%position%) td:contains("%orderNumber%")',
        ]);
    }

    private function getOrderLinkForRow(int $paymentNumber): NodeElement
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowsWithFields($table, [])[$paymentNumber];

        return $row->find('css', 'td:nth-child(2)');
    }

    private function getField(string $orderNumber, string $fieldName): NodeElement
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['number' => $orderNumber]);

        return $tableAccessor->getFieldFromRow($table, $row, $fieldName);
    }
}
