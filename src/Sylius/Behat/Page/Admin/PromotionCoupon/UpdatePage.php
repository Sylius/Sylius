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

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    /**
     * @param int $limit
     */
    public function setCustomerUsageLimit($limit)
    {
        $this->getDocument()->fillField('Per-Customer Usage Limit', $limit);
    }

    /**
     * @param \DateTime $date
     */
    public function setExpiresAt(\DateTime $date)
    {
        $timestamp = $date->getTimestamp();

        $this->getDocument()->fillField('Expires at', date('Y-m-d', $timestamp));
    }

    /**
     * @param int $limit
     */
    public function setUsageLimit($limit)
    {
        $this->getDocument()->fillField('Usage limit', $limit);
    }

    /**
     * @return NodeElement
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
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
