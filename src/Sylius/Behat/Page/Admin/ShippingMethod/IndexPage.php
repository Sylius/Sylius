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

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function chooseArchival(string $isArchival): void
    {
        if (!$this->areFiltersVisible()) {
            $this->toggleFilters();
        }

        $this->getElement('filter_archival')->selectOption($isArchival);
    }

    public function isArchivalFilterEnabled(): bool
    {
        $archival = $this->getDocument()->find('css', 'button:contains("Restore")');

        return null !== $archival;
    }

    public function archiveShippingMethod(string $name): void
    {
        $actions = $this->getActionsForResource(['name' => $name]);
        $actions->pressButton('archive');
        $this->getElement('confirm_action_button')->press();
    }

    public function deleteShippingMethod(string $name): void
    {
        $this->open();
        $this->deleteResourceOnPage(['name' => $name]);
        $this->getElement('confirm_action_button')->press();
    }

    public function restoreShippingMethod(string $name): void
    {
        $actions = $this->getActionsForResource(['name' => $name]);
        $actions->pressButton('Restore');
        $this->getElement('confirm_action_button')->press();
    }

    public function isShippingMethodDisabled(ShippingMethodInterface $shippingMethod): bool
    {
        $this->open();

        return null !== $this->getRowFor($shippingMethod)->find('css', '[data-test-status-disabled]');
    }

    public function isShippingMethodEnabled(ShippingMethodInterface $shippingMethod): bool
    {
        $this->open();

        return null !== $this->getRowFor($shippingMethod)->find('css', '[data-test-status-enabled]');
    }

    private function getRowFor(ResourceInterface $shippingMethod): NodeElement
    {
        return $this->getElement('row', ['%resourceId%' => $shippingMethod->getId()]);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'confirm_action_button' => '[data-confirm-btn-true]',
            'filter_archival' => '#criteria_archival',
            'row' => '[data-test-row][data-test-resource-id="%resourceId%"]',
        ]);
    }
}
