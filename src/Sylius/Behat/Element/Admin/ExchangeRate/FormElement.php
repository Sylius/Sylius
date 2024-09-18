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

namespace Sylius\Behat\Element\Admin\ExchangeRate;

use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;

final class FormElement extends BaseFormElement implements FormElementInterface
{
    public function isFieldDisabled(string $fieldName): bool
    {
        return null !== $this->getElement($fieldName)->getAttribute('disabled');
    }

    public function getRatio(): string
    {
        return $this->getElement('ratio')->getValue();
    }

    public function hasFormValidationError(string $expectedMessage): bool
    {
        return $expectedMessage === $this->getValidationErrors();
    }

    public function specifyRatio(string $ratio): void
    {
        $this->getElement('ratio')->setValue($ratio);
    }

    public function specifySourceCurrency(string $sourceCurrency): void
    {
        $this->getElement('source_currency')->setValue($sourceCurrency);
    }

    public function specifyTargetCurrency(string $targetCurrency): void
    {
        $this->getElement('target_currency')->setValue($targetCurrency);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'ratio' => '[data-test-ratio]',
            'source_currency' => '[data-test-source-currency]',
            'target_currency' => '[data-test-target-currency]',
        ]);
    }
}
