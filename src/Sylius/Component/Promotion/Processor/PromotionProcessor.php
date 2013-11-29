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

use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Checker\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Bundle\PromotionBundle\Repository\PromotionRepositoryInterface;

/**
 * Process all active promotions.
 *
 * Checks all rules and applies configured actions if rules are eligible.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionProcessor implements PromotionProcessorInterface
{
    protected $repository;
    protected $checker;
    protected $applicator;

    public function __construct(PromotionRepositoryInterface $repository, PromotionEligibilityCheckerInterface $checker, PromotionApplicatorInterface $applicator)
    {
        $this->repository = $repository;
        $this->checker = $checker;
        $this->applicator = $applicator;
    }

    public function process(PromotionSubjectInterface $subject)
    {
        foreach ($this->repository->findActive() as $promotion) {
            if ($this->checker->isEligible($subject, $promotion)) {
                $this->applicator->apply($subject, $promotion);
            }
        }
    }
}
