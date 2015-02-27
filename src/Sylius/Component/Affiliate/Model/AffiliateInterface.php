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

use Sylius\Component\Resource\Model\TimestampableInterface;

use Doctrine\Common\Collections\Collection;

interface AffiliateInterface extends TimestampableInterface
{
    /**
     * Get ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Get referral code.
     *
     * @return string
     */
    public function getReferralCode();

    /**
     * Set referral code.
     *
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
}
