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

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface ReferrerAwareInterface
{
    /**
     * Get referral.
     *
     * @return null|ReferralInterface
     */
    public function getReferrer();

    /**
     * Set referral.
     *
     * @param ReferralInterface $referral
     *
     * @return self
     */
    public function setReferrer(ReferralInterface $referral = null);
}
