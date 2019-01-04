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

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function specifyFilterDateFrom(string $dateTime): void
    {
        $dateAndTime = explode(' ', $dateTime);

        $this->getDocument()->fillField('criteria_date_from_date', $dateAndTime[0]);
        $this->getDocument()->fillField('criteria_date_from_time', $dateAndTime[1] ?? '');
    }

    public function specifyFilterDateTo(string $dateTime): void
    {
        $dateAndTime = explode(' ', $dateTime);

        $this->getDocument()->fillField('criteria_date_to_date', $dateAndTime[0]);
        $this->getDocument()->fillField('criteria_date_to_time', $dateAndTime[1] ?? '');
    }

    public function chooseChannelFilter(string $channelName): void
    {
        $this->getElement('filter_channel')->selectOption($channelName);
    }

    public function chooseCurrencyFilter(string $currencyName): void
    {
        $this->getElement('filter_currency')->selectOption($currencyName);
    }

    public function specifyFilterTotalGreaterThan(string $total): void
    {
        $this->getDocument()->fillField('criteria_total_greaterThan', $total);
    }

    public function specifyFilterTotalLessThan(string $total): void
    {
        $this->getDocument()->fillField('criteria_total_lessThan', $total);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_channel' => '#criteria_channel',
            'filter_currency' => '#criteria_total_currency',
        ]);
    }
}
