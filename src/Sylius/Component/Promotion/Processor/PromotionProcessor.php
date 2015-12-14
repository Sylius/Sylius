<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Processor;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Checker\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

/**
 * Process all active promotions.
 *
 * Checks all rules and applies configured actions if rules are eligible.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionProcessor implements PromotionProcessorInterface
{
    /**
     * @var PromotionRepositoryInterface
     */
    protected $repository;

    /**
     * @var PromotionEligibilityCheckerInterface
     */
    protected $checker;

    /**
     * @var PromotionApplicatorInterface
     */
    protected $applicator;

    /**
     * @var Collection|PromotionInterface[]
     */
    protected $promotions;

    /**
     * @param PromotionRepositoryInterface $repository
     * @param PromotionEligibilityCheckerInterface $checker
     * @param PromotionApplicatorInterface $applicator
     */
    public function __construct(PromotionRepositoryInterface $repository, PromotionEligibilityCheckerInterface $checker, PromotionApplicatorInterface $applicator)
    {
        $this->repository = $repository;
        $this->checker = $checker;
        $this->applicator = $applicator;
    }

    /**
     * @param PromotionSubjectInterface $subject
     */
    public function revert(PromotionSubjectInterface $subject)
    {
        foreach ($subject->getPromotions() as $promotion) {
            $this->applicator->revert($subject, $promotion);
        }
    }

    /**
     * @param PromotionSubjectInterface $subject
     */
    public function process(PromotionSubjectInterface $subject)
    {
        $eligiblePromotions = array();

        foreach ($this->getActivePromotions() as $promotion) {
            if (!$this->checker->isEligible($subject, $promotion)) {
                continue;
            }

            if ($promotion->isExclusive()) {
                $this->applicator->apply($subject, $promotion);
                return;
            }

            $eligiblePromotions[] = $promotion;
        }

        foreach ($eligiblePromotions as $promotion) {
            $this->applicator->apply($subject, $promotion);
        }
    }

    /**
     * @return Collection|PromotionInterface[]
     */
    protected function getActivePromotions()
    {
        if (null === $this->promotions) {
            $this->promotions = $this->repository->findActive();
        }

        return $this->promotions;
    }
}
