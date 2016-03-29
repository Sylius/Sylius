<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Checker;

use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\AffiliateGoalInterface;

interface ReferralEligibilityCheckerInterface
{
    /**
     * @param AffiliateGoalInterface    $goal
     * @param AffiliateInterface        $affiliate
     * @param mixed                     $subject
     *
     * @return Boolean
     */
    public function isEligible(AffiliateGoalInterface $goal, AffiliateInterface $affiliate, $subject = null);
}
