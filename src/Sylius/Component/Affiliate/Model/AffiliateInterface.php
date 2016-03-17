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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface AffiliateInterface extends ReferralInterface, TimestampableInterface, ResourceInterface
{
    const AFFILIATE_ENABLED  = 1;
    const AFFILIATE_PAUSED   = 0;
    const AFFILIATE_DISABLED = -1;

    const PROVISION_PERCENT  = 1;
    const PROVISION_FIXED    = 2;

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getReferralCode();

    /**
     * @param string $referralCode
     *
     * @return self
     */
    public function setReferralCode($referralCode);

    /**
     * Get referrals.
     *
     * @return Collection|ReferralInterface[]
     */
    public function getReferrals();

    /**
     * Add referral.
     *
     * @param ReferralInterface $referral
     *
     * @return self
     */
    public function addReferral(ReferralInterface $referral);

    /**
     * Remove referral.
     *
     * @param ReferralInterface $referral
     *
     * @return self
     */
    public function removeReferral(ReferralInterface $referral);

    /**
     * Check that referral was already aligned.
     *
     * @param ReferralInterface $referral
     *
     * @return bool
     */
    public function hasReferral(ReferralInterface $referral);

    /**
     * Check that affiliation is disabled.
     *
     * @return bool
     */
    public function isDisabled();

    /**
     * Check that affiliation is enabled.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Check that affiliation is paused.
     *
     * @return bool
     */
    public function isPaused();

    /**
     * Get affiliation status.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set affiliation status.
     *
     * @param int $status
     *
     * @return self
     */
    public function setStatus($status);

    /**
     * @return Collection|TransactionInterface[]
     */
    public function getTransactions();

    /**
     * @param TransactionInterface $transaction
     *
     * @return self
     */
    public function addTransaction(TransactionInterface $transaction);

    /**
     * @param TransactionInterface $transaction
     *
     * @return self
     */
    public function removeTransaction(TransactionInterface $transaction);
}
