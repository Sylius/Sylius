<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Action;

use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\GoalInterface;

interface AffiliationApplicatorInterface
{
    /**
     * @param object             $subject
     * @param AffiliateInterface $affiliate
     * @param GoalInterface      $goal
     */
    public function apply($subject, AffiliateInterface $affiliate, GoalInterface $goal);
}
