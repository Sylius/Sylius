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

namespace Sylius\Behat\Page\Admin\Currency;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use Toggles;

    public function canBeDisabled(): bool
    {
        $toggleableElement = $this->getToggleableElement();
        $this->assertCheckboxState($toggleableElement, true);

        return $toggleableElement->hasAttribute('disabled');
    }

    public function canHaveExchangeRateChanged(): bool
    {
        return $this->getElement('exchangeRate')->hasAttribute('disabled');
    }

    public function changeExchangeRate(string $exchangeRate): void
    {
        $this->getDocument()->fillField('Exchange rate', $exchangeRate);
    }

    public function getExchangeRateValue(): string
    {
        return $this->getElement('exchangeRate')->getValue();
    }

    public function getCodeDisabledAttribute(): string
    {
        return $this->getElement('code')->getAttribute('disabled');
    }

    protected function getToggleableElement(): NodeElement
    {
        return $this->getElement('enabled');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_currency_code',
            'enabled' => '#sylius_currency_enabled',
            'exchangeRate' => '#sylius_currency_exchangeRate',
        ]);
    }
}
