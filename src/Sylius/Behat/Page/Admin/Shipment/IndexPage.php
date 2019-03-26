<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Shipment;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function chooseChannelFilter(string $channelName): void
    {
        $this->getElement('filter_channel')->selectOption($channelName);
    }
}
