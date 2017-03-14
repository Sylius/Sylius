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
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class PromotionProcessor implements PromotionProcessorInterface
{
    /**
     * @var PreQualifiedPromotionsProviderInterface
     */
    private $preQualifiedPromotionsProvider;

    /**
     * @var PromotionEligibilityCheckerInterface
     */
    private $promotionEligibilityChecker;

    /**
     * @var PromotionApplicatorInterface
     */
    private $promotionApplicator;

    /**
     * @param PreQualifiedPromotionsProviderInterface $preQualifiedPromotionsProvider
     * @param PromotionEligibilityCheckerInterface $promotionEligibilityChecker
     * @param PromotionApplicatorInterface $promotionApplicator
     */
    public function __construct(
        PreQualifiedPromotionsProviderInterface $preQualifiedPromotionsProvider,
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        PromotionApplicatorInterface $promotionApplicator
    ) {
        $this->preQualifiedPromotionsProvider = $preQualifiedPromotionsProvider;
        $this->promotionEligibilityChecker = $promotionEligibilityChecker;
        $this->promotionApplicator = $promotionApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function process(PromotionSubjectInterface $subject)
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
