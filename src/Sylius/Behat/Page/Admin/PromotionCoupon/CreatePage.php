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

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

    public function setCustomerUsageLimit(int $limit): void
    {
        $this->getDocument()->fillField('Per-Customer Usage Limit', $limit);
    }

    public function setExpiresAt(\DateTimeInterface $date): void
    {
        $timestamp = $date->getTimestamp();

        $this->getDocument()->fillField('Expires at', date('Y-m-d', $timestamp));
    }

    public function setUsageLimit(int $limit): void
    {
        $this->getDocument()->fillField('Usage limit', $limit);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_promotion_coupon_code',
            'expires_at' => '#sylius_promotion_coupon_expiresAt',
            'usage_limit' => '#sylius_promotion_coupon_usageLimit',
            'usage_limit_per_customer' => '#sylius_promotion_coupon_usageLimit',
        ]);
    }
}
