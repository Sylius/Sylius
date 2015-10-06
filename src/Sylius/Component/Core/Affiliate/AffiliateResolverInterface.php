<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Affiliate;

use Sylius\Component\Affiliate\Model\AffiliateInterface;

/**
 * Interface for service defining the current referrer affiliate.
 *
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
interface AffiliateResolverInterface
{
    /**
     * Get currently used affiliate.
     *
     * @param string $referralCode
     *
     * @return AffiliateInterface
     */
    public function resolve($referralCode);
}
