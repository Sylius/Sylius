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

namespace Sylius\Behat\Element\Admin\TaxRate;

use FriendsOfBehat\PageObjectExtension\Element\Element;

class FilterElement extends Element implements FilterElementInterface
{
    public function specifyStartDateFrom(string $date): void
    {
        $this->getElement('start_date_from')->setValue($date);
    }

    public function specifyStartDateTo(string $date): void
    {
        $this->getElement('start_date_to')->setValue($date);
    }

    public function specifyEndDateFrom(string $date): void
    {
        $this->getElement('end_date_from')->setValue($date);
    }

    public function specifyEndDateTo(string $date): void
    {
        $this->getElement('end_date_to')->setValue($date);
    }

    public function filter(): void
    {
        $this->getElement('filter')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'end_date_from' => '#criteria_endDate_from_date',
            'end_date_to' => '#criteria_endDate_to_date',
            'filter' => 'button[type="submit"]:contains("Filter")',
            'start_date_from' => '#criteria_startDate_from_date',
            'start_date_to' => '#criteria_startDate_to_date',
        ]);
    }
}
