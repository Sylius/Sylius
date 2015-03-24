<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Action;

use Sylius\Component\Affiliate\Model\AffiliateInterface;

interface  AffiliationApplicatorInterface
{
    /**
     * @param object             $subject
     * @param AffiliateInterface $affiliate
     */
    public function apply($subject, AffiliateInterface $affiliate);
}
