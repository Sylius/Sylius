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

use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Checker\PromotionSubjectEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;

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
     * @var PreQualifiedPromotionsProviderInterface
     */
    protected $preQualifiedPromotionsProvider;

    /**
     * @var PromotionSubjectEligibilityCheckerInterface
     */
    protected $checker;

    /**
     * @var PromotionApplicatorInterface
     */
    protected $applicator;

    /**
     * @param PreQualifiedPromotionsProviderInterface $preQualifiedPromotionsProvider
     * @param PromotionSubjectEligibilityCheckerInterface $checker
     * @param PromotionApplicatorInterface $applicator
     */
    public function __construct(
        PreQualifiedPromotionsProviderInterface $preQualifiedPromotionsProvider,
        PromotionSubjectEligibilityCheckerInterface $checker,
        PromotionApplicatorInterface $applicator
    ) {
        $this->preQualifiedPromotionsProvider = $preQualifiedPromotionsProvider;
        $this->checker = $checker;
        $this->applicator = $applicator;
    }

    /**
     * @param PromotionSubjectInterface $subject
     *
     * @return mixed
     */
    public function process(PromotionSubjectInterface $subject)
    {
        foreach ($subject->getPromotions() as $promotion) {
            $this->applicator->revert($subject, $promotion);
        }

        $eligiblePromotions = [];

        foreach ($this->preQualifiedPromotionsProvider->getPromotions($subject) as $promotion) {
            if (!$this->checker->isEligible($subject, $promotion)) {
                continue;
            }

            if ($promotion->isExclusive()) {
                return $this->applicator->apply($subject, $promotion);
            }

            $eligiblePromotions[] = $promotion;
        }

        foreach ($eligiblePromotions as $promotion) {
            $this->applicator->apply($subject, $promotion);
        }
    }
}
