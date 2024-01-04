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

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Webmozart\Assert\Assert;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    public function setCustomerUsageLimit(int $limit): void
    {
        $this->getDocument()->fillField('Per-Customer Usage Limit', $limit);
    }

    public function setExpiresAt(\DateTimeInterface $date): void
    {
        $timestamp = $date->getTimestamp();

        $this->getDocument()->fillField('Expires at', date('Y-m-d', $timestamp));
    }

    public function setUsageLimit(string $limit): void
    {
        $this->getDocument()->fillField('Usage limit', $limit);
    }

    public function isReusableFromCancelledOrders(): bool
    {
        return $this->getElement('reusable_from_cancelled_orders')->isChecked();
    }

    public function toggleReusableFromCancelledOrders(bool $reusable): void
    {
        $toggle = $this->getElement('reusable_from_cancelled_orders');

        Assert::notSame($toggle->isChecked(), $reusable);

        $reusable ? $toggle->check() : $toggle->uncheck();
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_promotion_coupon_code',
            'expires_at' => '#sylius_promotion_coupon_expiresAt',
            'usage_limit' => '#sylius_promotion_coupon_usageLimit',
            'per_customer_usage_limit' => '#sylius_promotion_coupon_perCustomerUsageLimit',
            'reusable_from_cancelled_orders' => '#sylius_promotion_coupon_reusableFromCancelledOrders',
        ]);
    }
}
