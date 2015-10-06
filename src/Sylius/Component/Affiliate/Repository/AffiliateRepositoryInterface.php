<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Repository;

use Doctrine\ORM\NonUniqueResultException;

/**
 * Repository interface for affiliates.
 *
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
interface AffiliateRepositoryInterface
{
    /**
     * Find affiliate by referral code.
     *
     * @param string $referralCode
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findOneByReferralCode($referralCode);
}
