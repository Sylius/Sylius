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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Listener;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionFailed;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CatalogPromotionFailedListenerSpec extends ObjectBehavior
{
    function let(
        AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
        RepositoryInterface $catalogPromotionRepository,
        EntityManagerInterface $entityManager,
        FactoryInterface $stateMachine,
    ): void {
        $this->beConstructedWith($allProductVariantsCatalogPromotionsProcessor, $catalogPromotionRepository, $entityManager, $stateMachine);
    }

    function it_dispatches_transition_failed_on_a_failed_message(
        AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
        RepositoryInterface $catalogPromotionRepository,
        EntityManagerInterface $entityManager,
        CatalogPromotionInterface $catalogPromotion,
        FactoryInterface $stateMachine,
        StateMachineInterface $stateMachineInterface,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'WINTER_MUGS_SALE'])->willReturn($catalogPromotion);

        $stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH)->willReturn($stateMachineInterface);
        $stateMachineInterface->can(CatalogPromotionTransitions::TRANSITION_PROCESS)->willReturn(true);

        $allProductVariantsCatalogPromotionsProcessor->process()->shouldBeCalled();

        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_PROCESS)->shouldBeCalled();
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_FAIL)->shouldBeCalled();

        $entityManager->flush()->shouldBeCalled();

        $this(new CatalogPromotionFailed('WINTER_MUGS_SALE'));
    }

    function it_does_nothing_if_there_is_no_catalog_promotion_with_given_code(
        AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
        RepositoryInterface $catalogPromotionRepository,
        EntityManagerInterface $entityManager,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'WINTER_MUGS_SALE'])->willReturn(null);
        $catalogPromotionRepository->findAll()->shouldNotBeCalled();

        $allProductVariantsCatalogPromotionsProcessor->process()->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();

        $this(new CatalogPromotionFailed('WINTER_MUGS_SALE'));
    }
}
