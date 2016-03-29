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

use Sylius\Component\Affiliate\Repository\AffiliateGoalRepositoryInterface;

/**
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
class ActiveAffiliateGoalsProvider implements AffiliateGoalsProviderInterface
{
    /**
     * @var AffiliateGoalRepositoryInterface
     */
    protected $repository;

    /**
     * @param AffiliateGoalRepositoryInterface $repository
     */
    public function __construct(AffiliateGoalRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getAffiliateGoals($subject = null)
    {
        return $this->repository->findActive();
    }
}