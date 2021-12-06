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

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\CatalogPromotionVariantsProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;

final class CatalogPromotionProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider,
        CatalogPromotionApplicatorInterface $productCatalogPromotionApplicator,
        FactoryInterface $stateMachine
    ): void {
        $this->beConstructedWith(
            $catalogPromotionVariantsProvider,
            $productCatalogPromotionApplicator,
            $stateMachine
        );
    }

    function it_implements_catalog_promotion_processor_interface(): void
    {
        $this->shouldImplement(CatalogPromotionProcessorInterface::class);
    }

    function it_applies_catalog_promotion_on_eligible_variants(
        CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider,
        CatalogPromotionApplicatorInterface $productCatalogPromotionApplicator,
        CatalogPromotionInterface $catalogPromotion,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        FactoryInterface $stateMachine,
        StateMachineInterface $stateMachineInterface
    ): void {
        $catalogPromotion->isEnabled()->willReturn(true);
        $catalogPromotionVariantsProvider
            ->provideEligibleVariants($catalogPromotion)
            ->willReturn([$firstVariant, $secondVariant])
        ;

        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_PROCESSING);

        $stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH)->willReturn($stateMachineInterface);
        $stateMachineInterface->can(CatalogPromotionTransitions::TRANSITION_PROCESS)->willReturn(true);
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_PROCESS)->shouldBeCalled();
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_ACTIVATE)->shouldBeCalled();

        $productCatalogPromotionApplicator->applyOnVariant($firstVariant, $catalogPromotion)->shouldBeCalled();
        $productCatalogPromotionApplicator->applyOnVariant($secondVariant, $catalogPromotion)->shouldBeCalled();

        $this->process($catalogPromotion);
    }

    function it_does_nothing_if_there_are_no_eligible_variants(
        CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider,
        CatalogPromotionApplicatorInterface $productCatalogPromotionApplicator,
        CatalogPromotionInterface $catalogPromotion,
        FactoryInterface $stateMachine,
        StateMachineInterface $stateMachineInterface
    ): void {
        $catalogPromotion->isEnabled()->willReturn(true);
        $catalogPromotionVariantsProvider->provideEligibleVariants($catalogPromotion)->willReturn([]);
        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_PROCESSING);

        $stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH)->willReturn($stateMachineInterface);
        $stateMachineInterface->can(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)->willReturn(true);

        $stateMachineInterface->can(CatalogPromotionTransitions::TRANSITION_PROCESS)->willReturn(true);
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_PROCESS)->shouldBeCalled();
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)->shouldBeCalled();

        $productCatalogPromotionApplicator->applyOnVariant(Argument::any())->shouldNotBeCalled();

        $this->process($catalogPromotion);
    }

    function it_does_nothing_if_catalog_promotion_is_not_in_processing_state(
        CatalogPromotionVariantsProviderInterface $catalogPromotionVariantsProvider,
        CatalogPromotionApplicatorInterface $productCatalogPromotionApplicator,
        CatalogPromotionInterface $catalogPromotion,
        FactoryInterface $stateMachine,
        StateMachineInterface $stateMachineInterface
    ): void {
        $catalogPromotion->isEnabled()->willReturn(true);
        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_FAILED);

        $catalogPromotionVariantsProvider->provideEligibleVariants($catalogPromotion)->willReturn([]);

        $stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH)->willReturn($stateMachineInterface);

        $stateMachineInterface->can(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)->willReturn(true);
        $stateMachineInterface->can(CatalogPromotionTransitions::TRANSITION_PROCESS)->willReturn(true);
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_PROCESS)->shouldBeCalled();
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)->shouldBeCalled();

        $productCatalogPromotionApplicator->applyOnVariant(Argument::any())->shouldNotBeCalled();

        $this->process($catalogPromotion);
    }
}
