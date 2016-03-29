<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Provider;

use Sylius\Component\Affiliate\Model\AffiliateGoalInterface;

interface AffiliateGoalsProviderInterface
{
    /**
     * @param mixed $subject
     *
     * @return AffiliateGoalInterface[]
     */
    public function getAffiliateGoals($subject = null);
}