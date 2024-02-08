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

namespace Sylius\Behat\Element\Admin\CatalogPromotion;

use FriendsOfBehat\PageObjectExtension\Element\Element;
use Sylius\Component\Core\Model\ChannelInterface;

final class FilterElement extends Element implements FilterElementInterface
{
    private const BOOLEAN_FILTER_TRUE = 'Yes';

    public function chooseChannel(ChannelInterface $channel): void
    {
        $this->getElement('channel')->selectOption($channel->getName());
    }

    public function chooseEnabled(): void
    {
        $this->getElement('enabled')->selectOption(self::BOOLEAN_FILTER_TRUE);
    }

    public function chooseState(string $state): void
    {
        $this->getElement('state')->selectOption($state);
    }

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

    public function search(string $phrase): void
    {
        $this->getElement('search')->setValue($phrase);
    }

    public function filter(): void
    {
        $this->getElement('filter')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'channel' => '#criteria_channel',
            'enabled' => '#criteria_enabled',
            'end_date_from' => '#criteria_endDate_from_date',
            'end_date_to' => '#criteria_endDate_to_date',
            'filter' => 'button[type="submit"]:contains("Filter")',
            'search' => '#criteria_search_value',
            'start_date_from' => '#criteria_startDate_from_date',
            'start_date_to' => '#criteria_startDate_to_date',
            'state' => '#criteria_state',
        ]);
    }
}
