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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use SM\Factory\FactoryInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;

final class CatalogPromotionStateProcessor implements CatalogPromotionStateProcessorInterface
{
    public function __construct(
        private CatalogPromotionEligibilityCheckerInterface $catalogPromotionEligibilityChecker,
        private FactoryInterface $stateMachineFactory,
    ) {
    }

    public function process(CatalogPromotionInterface $catalogPromotion): void
    {
        $stateMachine = $this->stateMachineFactory->get($catalogPromotion, CatalogPromotionTransitions::GRAPH);

        if ($stateMachine->can(CatalogPromotionTransitions::TRANSITION_PROCESS)) {
            $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_PROCESS);

            return;
        }

        if (!$this->catalogPromotionEligibilityChecker->isCatalogPromotionEligible($catalogPromotion)) {
            if ($stateMachine->can(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)) {
                $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_DEACTIVATE);
            }

            return;
        }

        if ($stateMachine->can(CatalogPromotionTransitions::TRANSITION_ACTIVATE)) {
            $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_ACTIVATE);
        }
    }
}
