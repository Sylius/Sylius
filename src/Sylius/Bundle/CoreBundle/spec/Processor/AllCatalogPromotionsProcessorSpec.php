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
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionProcessorInterface;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Bundle\PromotionBundle\Criteria\Enabled;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;

final class AllCatalogPromotionsProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider,
        FactoryInterface $stateMachine,
        CriteriaInterface $firstCriteria,
        CriteriaInterface $secondCriteria
    ): void {
        $this->beConstructedWith(
            $catalogPromotionClearer,
            $catalogPromotionProcessor,
            $catalogPromotionsProvider,
            $stateMachine,
            [$firstCriteria, $secondCriteria]
        );
    }

    function it_clears_and_processes_catalog_promotions(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider,
        CatalogPromotionInterface $firstCatalogPromotion,
        CatalogPromotionInterface $secondCatalogPromotion,
        FactoryInterface $stateMachine,
        StateMachineInterface $stateMachineInterfaceFirst,
        StateMachineInterface $stateMachineInterfaceSecond,
        CriteriaInterface $firstCriteria,
        CriteriaInterface $secondCriteria
    ): void {
        $catalogPromotionClearer->clear()->shouldBeCalled();

        $stateMachine->get($firstCatalogPromotion, CatalogPromotionTransitions::GRAPH)->willReturn($stateMachineInterfaceFirst);
        $stateMachine->get($secondCatalogPromotion, CatalogPromotionTransitions::GRAPH)->willReturn($stateMachineInterfaceSecond);

        $catalogPromotionsProvider
            ->provide([$firstCriteria, $secondCriteria])
            ->willReturn([$firstCatalogPromotion, $secondCatalogPromotion])
        ;

        $stateMachineInterfaceFirst->apply(CatalogPromotionTransitions::TRANSITION_PROCESS)->shouldBeCalled();
        $stateMachineInterfaceSecond->apply(CatalogPromotionTransitions::TRANSITION_PROCESS)->shouldBeCalled();

        $catalogPromotionProcessor->process($firstCatalogPromotion)->shouldBeCalled();
        $catalogPromotionProcessor->process($secondCatalogPromotion)->shouldBeCalled();

        $this->process();
    }
}
