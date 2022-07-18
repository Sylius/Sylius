<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;

final class CatalogPromotionStateProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionEligibilityCheckerInterface $catalogPromotionEligibilityChecker,
        FactoryInterface $stateMachineFactory,
    ): void {
        $this->beConstructedWith($catalogPromotionEligibilityChecker, $stateMachineFactory);
    }

    function it_implements_catalog_promotion_state_processor_interface(): void
    {
        $this->shouldImplement(CatalogPromotionStateProcessorInterface::class);
    }

    function it_activates_a_catalog_promotion(
        CatalogPromotionEligibilityCheckerInterface $catalogPromotionEligibilityChecker,
        CatalogPromotionInterface $catalogPromotion,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
    ): void {
        $stateMachineFactory->get($catalogPromotion, CatalogPromotionTransitions::GRAPH)->willReturn($stateMachine);

        $catalogPromotionEligibilityChecker->isCatalogPromotionEligible($catalogPromotion)->willReturn(true);

        $stateMachine->can(CatalogPromotionTransitions::TRANSITION_ACTIVATE)->willReturn(true);
        $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_ACTIVATE)->shouldBeCalled();

        $this->process($catalogPromotion);
    }

    function it_deactivates_a_catalog_promotion(
        CatalogPromotionEligibilityCheckerInterface $catalogPromotionEligibilityChecker,
        CatalogPromotionInterface $catalogPromotion,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachineInterface,
    ): void {
        $stateMachineFactory->get($catalogPromotion, CatalogPromotionTransitions::GRAPH)->willReturn($stateMachineInterface);

        $catalogPromotionEligibilityChecker->isCatalogPromotionEligible($catalogPromotion)->willReturn(false);

        $stateMachineInterface->can(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)->willReturn(true);
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)->shouldBeCalled();

        $this->process($catalogPromotion);
    }
}
