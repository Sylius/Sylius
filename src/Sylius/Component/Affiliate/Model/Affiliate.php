<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Affiliate implements AffiliateInterface
{
    /**
     * Affiliate id.
     *
     * @var int
     */
    protected $id;

    /**
     * Referral code.
     *
     * @var string
     */
    protected $referralCode;

    /**
     * @var Collection|ReferralInterface[]
     */
    protected $referrals;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Last update time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->referrals = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferralCode()
    {
        return $this->referralCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferrals()
    {
        return $this->referrals;
    }

    /**
     * {@inheritdoc}
     */
    public function setReferralCode($referralCode)
    {
        $this->referralCode = $referralCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addReferral(ReferralInterface $referral)
    {
        if (!$this->hasReferral($referral)) {
            $this->referrals->add($referral);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeReferral(ReferralInterface $referral)
    {
        if ($this->hasReferral($referral)) {
            $this->referrals->removeElement($referral);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasReferral(ReferralInterface $referral)
    {
        return $this->referrals->contains($referral);
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
}
