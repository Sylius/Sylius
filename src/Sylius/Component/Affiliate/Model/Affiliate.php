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

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Affiliate implements AffiliateInterface
{
    /**
     * Affiliate id.
     *
     * @var int
     */
    protected $id;

    /**
     * Affiliation status.
     *
     * @var int
     */
    protected $status = AffiliateInterface::AFFILIATE_ENABLED;

    /**
     * @var ReferrerInterface[]
     */
    protected $referrer;

    /**
     * @var string
     */
    protected $referralCode;

    /**
     * @var Collection|ReferralInterface[]
     */
    protected $referrals;

    /**
     * @var Collection|ReferralInterface[]
     */
    protected $transactions;

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
        $this->referrals    = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->createdAt    = new \DateTime();
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
    public function isDisabled()
    {
        return AffiliateInterface::AFFILIATE_DISABLED === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return AffiliateInterface::AFFILIATE_ENABLED === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function isPaused()
    {
        return AffiliateInterface::AFFILIATE_PAUSED === $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
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
    public function setReferralCode($referralCode)
    {
        $this->referralCode = $referralCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * {@inheritdoc}
     */
    public function setReferrer(ReferralInterface $referral = null)
    {
        $this->referrer = $referral;

        return $this;
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
    public function hasReferral(ReferralInterface $referral)
    {
        return $this->referrals->contains($referral);
    }

    /**
     * {@inheritdoc}
     */
    public function addReferral(ReferralInterface $referral)
    {
        if (!$this->hasReferral($referral)) {
            $this->referrals->add($referral);
            $referral->setReferrer($this);
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
            $referral->setReferrer(null);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * {@inheritdoc}
     */
    public function addTransaction(TransactionInterface $transaction)
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);

            $transaction->setAffiliate($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeTransaction(TransactionInterface $transaction)
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);

            $transaction->setAffiliate(null);
        }

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
}
