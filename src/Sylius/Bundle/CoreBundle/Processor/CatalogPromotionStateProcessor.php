<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Processor;

use SM\Factory\FactoryInterface;
use Sylius\Bundle\CoreBundle\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;

final class CatalogPromotionStateProcessor implements CatalogPromotionStateProcessorInterface
{
    public function __construct(
        private CatalogPromotionEligibilityCheckerInterface $catalogPromotionEligibilityChecker,
        private FactoryInterface $stateMachine
    ) {
    }

    public function process(CatalogPromotionInterface $catalogPromotion): void
    {
        $stateMachine = $this->stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH);

        if ($stateMachine->can(CatalogPromotionTransitions::TRANSITION_PROCESS)) {
            $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_PROCESS);
        }

        if (
            !$this->catalogPromotionEligibilityChecker->isCatalogPromotionEligible($catalogPromotion) ||
            !$this->catalogPromotionEligibilityChecker->isCatalogPromotionEligibleOperatingTime($catalogPromotion)
        ) {
            if ($stateMachine->can(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)) {
                $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_DEACTIVATE);
            }

            return;
        }

        $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_ACTIVATE);
    }
}
