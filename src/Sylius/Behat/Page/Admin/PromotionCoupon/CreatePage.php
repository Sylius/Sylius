<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

    /**
     * {@inheritdoc}
     */
    public function setCustomerUsageLimit($limit)
    {
        $this->getDocument()->fillField('Per-Customer Usage Limit', $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiresAt(\DateTime $date)
    {
        $timestamp = $date->getTimestamp();

        $this->getDocument()->fillField('Expires at', date('Y-m-d', $timestamp));
    }

    /**
     * {@inheritdoc}
     */
    public function setUsageLimit($limit)
    {
        $this->getDocument()->fillField('Usage limit', $limit);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_promotion_coupon_code',
            'expires_at' => '#sylius_promotion_coupon_expiresAt',
            'usage_limit' => '#sylius_promotion_coupon_usageLimit',
            'usage_limit_per_customer' => '#sylius_promotion_coupon_usageLimit',
        ]);
    }
}
