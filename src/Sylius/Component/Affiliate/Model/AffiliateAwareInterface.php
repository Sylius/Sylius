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

interface AffiliateAwareInterface
{
    /**
     * Get affiliate.
     *
     * @return AffiliateInterface
     */
    public function getAffiliate();

    /**
     * Set affiliate.
     *
     * @param AffiliateInterface $affiliate
     *
     * @return self
     */
    public function setAffiliate(AffiliateInterface $affiliate = null);
}
