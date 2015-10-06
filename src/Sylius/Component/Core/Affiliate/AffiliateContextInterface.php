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
use Sylius\Component\Affiliate\Context\AffiliateContextInterface as BaseAffiliateContextInterface;

/**
 * Interface for service defining the current affiliate.
 *
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
interface AffiliateContextInterface extends BaseAffiliateContextInterface
{
    /**
     * {@inheritdoc}
     *
     * @return AffiliateInterface
     */
    public function getAffiliate();
}
