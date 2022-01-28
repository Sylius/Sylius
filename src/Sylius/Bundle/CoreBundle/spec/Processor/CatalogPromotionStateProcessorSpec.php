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

namespace spec\Sylius\Bundle\CoreBundle\Processor;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;

final class CatalogPromotionStateProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionEligibilityCheckerInterface $catalogPromotionEligibilityChecker,
        FactoryInterface $stateMachine
    ): void {
        $this->beConstructedWith($catalogPromotionEligibilityChecker, $stateMachine);
    }

    function it_implements_catalog_promotion_state_processor_interface(): void
    {
        $this->shouldImplement(CatalogPromotionStateProcessorInterface::class);
    }

    function it_activate_catalog_promotion(
        CatalogPromotionEligibilityCheckerInterface $catalogPromotionEligibilityChecker,
        CatalogPromotionInterface $catalogPromotion,
        FactoryInterface $stateMachine,
        StateMachineInterface $stateMachineInterface
    ): void {
        $stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH)->willReturn($stateMachineInterface);
        $stateMachineInterface->can(CatalogPromotionTransitions::TRANSITION_PROCESS)->willReturn(true);
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_PROCESS)->shouldBeCalled();

        $catalogPromotionEligibilityChecker->isCatalogPromotionEligible($catalogPromotion)->willReturn(true);
        $catalogPromotionEligibilityChecker->isCatalogPromotionEligibleOperatingTime($catalogPromotion)->willReturn(true);

        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_ACTIVATE)->shouldBeCalled();

        $this->process($catalogPromotion);
    }

    function it_deactivate_catalog_promotion(
        CatalogPromotionEligibilityCheckerInterface $catalogPromotionEligibilityChecker,
        CatalogPromotionInterface $catalogPromotion,
        FactoryInterface $stateMachine,
        StateMachineInterface $stateMachineInterface
    ): void {
        $stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH)->willReturn($stateMachineInterface);
        $stateMachineInterface->can(CatalogPromotionTransitions::TRANSITION_PROCESS)->willReturn(true);
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_PROCESS)->shouldBeCalled();

        $catalogPromotionEligibilityChecker->isCatalogPromotionEligible($catalogPromotion)->willReturn(false);
        $catalogPromotionEligibilityChecker->isCatalogPromotionEligibleOperatingTime($catalogPromotion)->willReturn(true);

        $stateMachineInterface->can(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)->willReturn(true);
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)->shouldBeCalled();

        $this->process($catalogPromotion);
    }
}
