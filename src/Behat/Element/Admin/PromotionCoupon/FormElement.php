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

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;

final class FormElement extends BaseFormElement implements FormElementInterface
{
    use SpecifiesItsField;
    use ChecksCodeImmutability;

    public function setUsageLimit(int $limit): void
    {
        $this->getElement('usage_limit')->setValue($limit);
    }

    public function setCustomerUsageLimit(int $limit): void
    {
        $this->getElement('per_customer_usage_limit')->setValue($limit);
    }

    public function setExpiresAt(\DateTimeInterface $date): void
    {
        $this->getElement('expires_at')->setValue($date->format('Y-m-d'));
    }

    public function toggleReusableFromCancelledOrders(bool $reusable): void
    {
        $this->getElement('reusable_from_cancelled_orders')->setValue($reusable);
    }

    public function isReusableFromCancelledOrders(): bool
    {
        return $this->getElement('reusable_from_cancelled_orders')->isChecked();
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '[data-test-code]',
            'expires_at' => '[data-test-expires-at]',
            'per_customer_usage_limit' => '[data-test-per-customer-usage-limit]',
            'reusable_from_cancelled_orders' => '[data-test-reusable-from-cancelled-orders]',
            'usage_limit' => '[data-test-usage-limit]',
        ]);
    }
}
