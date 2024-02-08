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

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function getRatio(): string
    {
        return $this->getElement('ratio')->getValue();
    }

    public function changeRatio(string $ratio): void
    {
        $this->getElement('ratio')->setValue($ratio);
    }

    public function isSourceCurrencyDisabled(): bool
    {
        return null !== $this->getElement('sourceCurrency')->getAttribute('disabled');
    }

    public function isTargetCurrencyDisabled(): bool
    {
        return null !== $this->getElement('targetCurrency')->getAttribute('disabled');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'ratio' => '#sylius_exchange_rate_ratio',
            'sourceCurrency' => '#sylius_exchange_rate_sourceCurrency',
            'targetCurrency' => '#sylius_exchange_rate_targetCurrency',
        ]);
    }
}
