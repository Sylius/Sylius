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

use Sylius\Component\Affiliate\Checker\ReferralEligibilityCheckerInterface;
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\GoalInterface;
use Sylius\Component\Affiliate\Provision\ProvisionApplicatorInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ReferralProcessor implements ReferralProcessorInterface
{
    protected $repository;
    protected $checker;
    protected $applicator;

    /**
     * @var GoalInterface[]
     */
    protected $goals;

    public function __construct(RepositoryInterface $repository, ReferralEligibilityCheckerInterface $checker, ProvisionApplicatorInterface $applicator)
    {
        $this->repository = $repository;
        $this->checker    = $checker;
        $this->applicator = $applicator;
    }

    public function process($subject, AffiliateInterface $affiliate)
    {
        if (in_array($affiliate->getStatus(), array(AffiliateInterface::AFFILIATE_PAUSED, AffiliateInterface::AFFILIATE_DISABLED))) {
            return;
        }

        foreach ($this->getActiveGoals() as $goal) {
            if (!$this->checker->isEligible($goal, $affiliate, $subject)) {
                continue;
            }

            $this->applicator->apply($subject, $affiliate, $goal);
        }
    }

    protected function getActiveGoals()
    {
        if (null !== $this->goals) {
            return $this->goals;
        }

        return $this->goals = $this->repository->findBy(array('deletedAt' => null));
    }
}
