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

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Referral code based affiliate resolver.
 *
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
class AffiliateResolver implements AffiliateResolverInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $affiliateRepository;

    /**
     * @param RepositoryInterface $affiliateRepository
     */
    public function __construct(RepositoryInterface $affiliateRepository) {
        $this->affiliateRepository = $affiliateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($referralCode)
    {
        return $this->affiliateRepository->findOneByReferralCode($referralCode);
    }
}
