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

/**
 * Coupon model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Coupon implements CouponInterface
{
    /**
     * Id
     *
     * @var integer
     */
    protected $id;

    /**
     * Coupon code
     *
     * @var string
     */
    protected $code;

    /**
     * Usage limit
     *
     * @var integer
     */
    protected $usageLimit;

    /**
     * Number of times used
     *
     * @var integer
     */
    protected $used = 0;

    /**
     * Associated promotion
     *
     * @var PromotionInterface
     */
    protected $promotion;

    /**
     * Expiration date
     *
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Modification time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Deletion time.
     *
     * @var \DateTime
     */
    protected $deletedAt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsageLimit()
    {
        return $this->usageLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsageLimit($usageLimit)
    {
        $this->usageLimit = $usageLimit;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsed($used)
    {
        $this->used = $used;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementUsed()
    {
        $this->used++;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * {@inheritdoc}
     */
    public function setPromotion(PromotionInterface $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiresAt(\DateTime $expiresAt = null)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isDeleted()
    {
        return null !== $this->deletedAt && new \DateTime() >= $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        if (null !== $this->usageLimit && $this->used >= $this->usageLimit) {
            return false;
        }

        if (null !== $this->expiresAt && $this->expiresAt < new \DateTime()) {
            return false;
        }

        return true;
    }
}
