<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Context;

use Sylius\Component\Affiliate\Model\AffiliateInterface;

/**
 * Provides the context of current affiliate.
 *
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
interface AffiliateContextInterface
{
    /**
     * Get the currently active affiliate.
     *
     * @return AffiliateInterface
     */
    public function getAffiliate();

    /**
     * Set the currently active affiliate.
     *
     * @param AffiliateInterface $affiliate
     */
    public function setAffiliate(AffiliateInterface $affiliate);

    /**
     * Does the context contain an affiliate
     *
     * @return bool
     */
    public function hasAffiliate();
}
