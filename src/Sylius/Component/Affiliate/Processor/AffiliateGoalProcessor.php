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

use Sylius\Component\Affiliate\Action\AffiliationApplicatorInterface;
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\GoalInterface;
use Sylius\Component\Resource\Checker\EligibilityCheckerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class AffiliateGoalProcessor implements AffiliateProcessorInterface
{
    protected $repository;
    protected $checker;
    protected $applicator;

    /**
     * @var GoalInterface[]
     */
    protected $goals;

    public function __construct(RepositoryInterface $repository, EligibilityCheckerInterface $checker, AffiliationApplicatorInterface $applicator)
    {
        $this->repository = $repository;
        $this->checker = $checker;
        $this->applicator = $applicator;
    }

    public function process($subject, AffiliateInterface $affiliate)
    {
        foreach ($this->getActiveGoals() as $goal) {
            if (!$this->checker->isEligible($subject, $goal)) {
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
