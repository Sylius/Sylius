<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Shipment;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function chooseShipmentFilter(string $shipmentState): void
    {
        $this->getElement('filter_state')->selectOption($shipmentState);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_state' => '#criteria_state',
        ]);
    }
}
