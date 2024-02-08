<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Promotion\Processor;

use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;

final class PromotionProcessor implements PromotionProcessorInterface
{
    public function __construct(
        private PreQualifiedPromotionsProviderInterface $preQualifiedPromotionsProvider,
        private PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        private PromotionApplicatorInterface $promotionApplicator,
    ) {
    }

    public function process(PromotionSubjectInterface $subject): void
    {
        foreach ($subject->getPromotions() as $promotion) {
            $this->promotionApplicator->revert($subject, $promotion);
        }

        $preQualifiedPromotions = $this->preQualifiedPromotionsProvider->getPromotions($subject);

        foreach ($preQualifiedPromotions as $promotion) {
            if ($promotion->isExclusive() && $this->promotionEligibilityChecker->isEligible($subject, $promotion)) {
                $this->promotionApplicator->apply($subject, $promotion);

                return;
            }
        }

        foreach ($preQualifiedPromotions as $promotion) {
            if (!$promotion->isExclusive() && $this->promotionEligibilityChecker->isEligible($subject, $promotion)) {
                $this->promotionApplicator->apply($subject, $promotion);
            }
        }
    }
}
