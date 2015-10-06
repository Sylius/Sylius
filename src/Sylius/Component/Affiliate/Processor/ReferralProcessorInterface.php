<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Processor;

use Sylius\Component\Affiliate\Model\AffiliateInterface;

interface ReferralProcessorInterface
{
    /**
     * @param object             $subject
     * @param AffiliateInterface $affiliate
     *
     * @return mixed
     */
    public function process($subject, AffiliateInterface $affiliate);
}
