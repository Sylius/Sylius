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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveInactiveCatalogPromotion;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Promotion\Exception\CatalogPromotionNotFoundException;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionRemovalProcessorSpec extends ObjectBehavior
{
    public function let(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        MessageBusInterface $commandBus,
        MessageBusInterface $eventBus,
    ): void {
        $this->beConstructedWith($catalogPromotionRepository, $commandBus, $eventBus);
    }

    public function it_removes_an_active_catalog_promotion_by_disabling_it_and_dispatching_catalog_promotion_ended_event_and_remove_inactive_catalog_promotion_command(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        MessageBusInterface $commandBus,
        MessageBusInterface $eventBus,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);

        $catalogPromotion->getState()->willReturn('active');

        $catalogPromotion->setEnabled(false)->shouldBeCalled();

        $event = new CatalogPromotionEnded('CATALOG_PROMOTION_CODE');
        $command = new RemoveInactiveCatalogPromotion('CATALOG_PROMOTION_CODE');

        $eventBus->dispatch($event)->willReturn(new Envelope($event));
        $commandBus->dispatch($command)->willReturn(new Envelope($command));

        $this->removeCatalogPromotion('CATALOG_PROMOTION_CODE');
    }

    public function it_removes_an_inactive_catalog_promotion_by_dispatching_remove_inactive_catalog_promotion_command_without_recalculating_the_catalog(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        MessageBusInterface $commandBus,
        MessageBusInterface $eventBus,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);

        $catalogPromotion->getState()->willReturn('inactive');

        $catalogPromotion->setEnabled(Argument::any())->shouldNotBeCalled();

        $event = new CatalogPromotionEnded('CATALOG_PROMOTION_CODE');
        $command = new RemoveInactiveCatalogPromotion('CATALOG_PROMOTION_CODE');

        $eventBus->dispatch($event)->shouldNotBeCalled();
        $commandBus->dispatch($command)->willReturn(new Envelope($command));

        $this->removeCatalogPromotion('CATALOG_PROMOTION_CODE');
    }

    public function it_does_not_dispatch_any_events_and_commands_if_catalog_promotion_from_command_does_not_exist(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        MessageBusInterface $commandBus,
        MessageBusInterface $eventBus,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn(null);

        $catalogPromotion->getState()->shouldNotBeCalled();

        $catalogPromotion->setEnabled(Argument::any())->shouldNotBeCalled();

        $event = new CatalogPromotionUpdated('CATALOG_PROMOTION_CODE');
        $command = new RemoveInactiveCatalogPromotion('CATALOG_PROMOTION_CODE');

        $eventBus->dispatch($event)->shouldNotBeCalled();
        $commandBus->dispatch($command)->shouldNotBeCalled();

        $this
            ->shouldThrow(CatalogPromotionNotFoundException::class)
            ->during('removeCatalogPromotion', ['CATALOG_PROMOTION_CODE'])
        ;
    }

    public function it_throws_an_exception_if_catalog_promotion_is_being_processed(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        MessageBusInterface $commandBus,
        MessageBusInterface $eventBus,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);

        $catalogPromotion->getState()->willReturn('processing');

        $catalogPromotion->setEnabled(Argument::any())->shouldNotBeCalled();

        $event = new CatalogPromotionEnded('CATALOG_PROMOTION_CODE');
        $command = new RemoveInactiveCatalogPromotion('CATALOG_PROMOTION_CODE');

        $eventBus->dispatch($event)->shouldNotBeCalled();
        $commandBus->dispatch($command)->shouldNotBeCalled();

        $this
            ->shouldThrow(InvalidCatalogPromotionStateException::class)
            ->during('removeCatalogPromotion', ['CATALOG_PROMOTION_CODE'])
        ;
    }

    public function it_throws_an_exception_if_catalog_promotion_state_is_out_of_invalid_one(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        MessageBusInterface $commandBus,
        MessageBusInterface $eventBus,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);

        $catalogPromotion->getState()->willReturn('invalid_state');

        $catalogPromotion->setEnabled(Argument::any())->shouldNotBeCalled();

        $event = new CatalogPromotionEnded('CATALOG_PROMOTION_CODE');
        $command = new RemoveInactiveCatalogPromotion('CATALOG_PROMOTION_CODE');

        $eventBus->dispatch($event)->shouldNotBeCalled();
        $commandBus->dispatch($command)->shouldNotBeCalled();

        $this
            ->shouldThrow(\DomainException::class)
            ->during('removeCatalogPromotion', ['CATALOG_PROMOTION_CODE'])
        ;
    }
}
