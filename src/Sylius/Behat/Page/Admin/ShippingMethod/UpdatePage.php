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

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;
    use Toggles;

    public function isAvailableInChannel(string $channelName): bool
    {
        return $this->getElement('channel', ['%channel%' => $channelName])->hasAttribute('checked');
    }

    public function removeZone(): void
    {
        $this->getDocument()->selectFieldOption('Zone', 'Select');
    }

    public function getValidationMessageForAmount(string $channelCode): string
    {
        $foundElement = $this->getElement('amount', ['%channelCode%' => $channelCode]);

        return $foundElement->find('css', '.sylius-validation-error')->getText();
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getToggleableElement(): NodeElement
    {
        return $this->getElement('enabled');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'amount' => '#sylius_shipping_method_configuration_%channelCode%_amount',
            'channel' => '.checkbox:contains("%channel%") input',
            'code' => '#sylius_shipping_method_code',
            'enabled' => '#sylius_shipping_method_enabled',
            'name' => '#sylius_shipping_method_translations_en_US_name',
            'zone' => '#sylius_shipping_method_zone',
        ]);
    }
}
