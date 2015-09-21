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

use Sylius\Component\Resource\Model\SoftDeletableInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface CouponInterface extends SoftDeletableInterface, TimestampableInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

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
