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

namespace Sylius\Behat\Element\Admin\PromotionCoupon;

use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;

final class GenerateFormElement extends BaseFormElement implements GenerateFormElementInterface
{
    public function specifyPrefix(string $prefix): void
    {
        $this->getElement('prefix')->setValue($prefix);
    }

    public function specifyCodeLength(?int $codeLength): void
    {
        $this->getElement('code_length')->setValue($codeLength);
    }

    public function specifySuffix(string $suffix): void
    {
        $this->getElement('suffix')->setValue($suffix);
    }

    public function specifyAmount(?int $amount): void
    {
        $this->getElement('amount')->setValue($amount);
    }

    public function setExpiresAt(\DateTimeInterface $date): void
    {
        $this->getElement('expires_at')->setValue($date->format('Y-m-d'));
    }

    public function setUsageLimit(int $limit): void
    {
        $this->getElement('usage_limit')->setValue($limit);
    }

    public function getFormValidationMessage(): string
    {
        return $this->getElement('form_validation_message')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'amount' => '[data-test-amount]',
            'code_length' => '[data-test-code-length]',
            'expires_at' => '[data-test-expires-at]',
            'form_validation_message' => 'form div.alert.alert-danger.d-block',
            'prefix' => '[data-test-prefix]',
            'suffix' => '[data-test-suffix]',
            'usage_limit' => '[data-test-usage-limit]',
        ]);
    }
}
