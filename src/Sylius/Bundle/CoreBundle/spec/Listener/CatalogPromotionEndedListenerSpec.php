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

namespace spec\Sylius\Bundle\CoreBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\Processor\AllCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CatalogPromotionEndedListenerSpec extends ObjectBehavior
{
    function let(
        AllCatalogPromotionsProcessorInterface $catalogPromotionReprocessor,
        RepositoryInterface $catalogPromotionRepository,
        EntityManagerInterface $entityManager,
        FactoryInterface $stateMachine
    ): void {
        $this->beConstructedWith($catalogPromotionReprocessor, $catalogPromotionRepository, $entityManager, $stateMachine);
    }

    function it_processes_catalog_promotion_that_has_just_ended(
        AllCatalogPromotionsProcessorInterface $catalogPromotionReprocessor,
        RepositoryInterface $catalogPromotionRepository,
        EntityManagerInterface $entityManager,
        CatalogPromotionInterface $catalogPromotion,
        FactoryInterface $stateMachine,
        StateMachineInterface $stateMachineInterface
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'WINTER_MUGS_SALE'])->willReturn($catalogPromotion);

        $stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH)->willReturn($stateMachineInterface);
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_PROCESS)->shouldBeCalled();

        $catalogPromotionReprocessor->process()->shouldBeCalled();

        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)->shouldBeCalled();

        $entityManager->flush()->shouldBeCalled();

        $this(new CatalogPromotionEnded('WINTER_MUGS_SALE'));
    }

    function it_does_nothing_if_there_is_no_catalog_promotion_with_given_code(
        AllCatalogPromotionsProcessorInterface $catalogPromotionReprocessor,
        RepositoryInterface $catalogPromotionRepository,
        EntityManagerInterface $entityManager
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'WINTER_MUGS_SALE'])->willReturn(null);
        $catalogPromotionRepository->findAll()->shouldNotBeCalled();

        $catalogPromotionReprocessor->process()->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();

        $this(new CatalogPromotionEnded('WINTER_MUGS_SALE'));
    }
}
