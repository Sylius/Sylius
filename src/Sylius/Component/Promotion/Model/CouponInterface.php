<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\SoftDeletableInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface CouponInterface extends CodeAwareInterface, SoftDeletableInterface, TimestampableInterface, ResourceInterface
{
    const TYPE_COUPON    = 'coupon';
    const TYPE_GIFT_CARD = 'gift_card';

    /**
     * @return int
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return int
     */
    public function getUsageLimit();

    /**
     * @param int $usageLimit
     */
    public function setUsageLimit($usageLimit);

    /**
     * @return int
     */
    public function getUsed();

    /**
     * @param int $used
     */
    public function setUsed($used);

    public function incrementUsed();

    public function decrementUsed();

    /**
     * @return PromotionInterface
     */
    public function getPromotion();

    /**
     * @param PromotionInterface $promotion
     */
    public function setPromotion(PromotionInterface $promotion = null);

    /**
     * @return \DateTime
     */
    public function getAmount();

    /**
     * @param int $amount
     */
    public function setAmount($amount);

    /**
     * @return null|\DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @return null|\DateTime
     */
    public function getExpiresAt();

    /**
     * @param \DateTime $expiresAt
     */
    public function setExpiresAt(\DateTime $expiresAt = null);

    /**
     * @return bool
     */
    public function isValid();
}
