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

use Sylius\Component\Affiliate\Model\GoalInterface;
use Sylius\Component\Resource\Checker\EligibilityChecker;

class AffiliateEligibilityChecker extends EligibilityChecker
{
    /**
     * {@inheritdoc}
     */
    protected function supports($subject, $object)
    {
        if (!$object instanceof GoalInterface) {
            return false;
        }

        return true;
    }
}
