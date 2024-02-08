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

namespace Sylius\Behat\Page\Admin\ExchangeRate;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    public function specifyRatio(string $ratio): void
    {
        $this->getDocument()->fillField('Ratio', $ratio);
    }

    public function chooseSourceCurrency(string $currency): void
    {
        $this->getDocument()->selectFieldOption('Source currency', $currency);
    }

    public function chooseTargetCurrency(string $currency): void
    {
        $this->getDocument()->selectFieldOption('Target currency', $currency);
    }

    public function hasFormValidationError(string $expectedMessage): bool
    {
        $formValidationErrors = $this->getDocument()->find('css', 'form > div.ui.red.label.sylius-validation-error');
        if (null === $formValidationErrors) {
            return false;
        }

        return $expectedMessage === $formValidationErrors->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'source currency' => '#sylius_exchange_rate_sourceCurrency',
            'target currency' => '#sylius_exchange_rate_targetCurrency',
            'ratio' => '#sylius_exchange_rate_ratio',
        ]);
    }
}
