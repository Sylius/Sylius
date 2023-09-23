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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionArchivalAnnouncerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\ArchiveCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\DisableCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RestoreCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionArchivalAnnouncerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $commandBus): void
    {
        $this->beConstructedWith($commandBus);
    }

    function it_implements_catalog_promotion_archival_announcer_interface(): void
    {
        $this->shouldImplement(CatalogPromotionArchivalAnnouncerInterface::class);
    }

    function it_dispatches_archive_catalog_promotion_command_on_enabled_catalog_promotion(
        MessageBusInterface $commandBus,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotion->getCode()->willReturn('CATALOG_PROMOTION_CODE');
        $catalogPromotion->isEnabled()->willReturn(true);

        $updateCatalogPromotionStateCommand = new UpdateCatalogPromotionState('CATALOG_PROMOTION_CODE');
        $disableCatalogPromotionCommand = new DisableCatalogPromotion('CATALOG_PROMOTION_CODE');
        $archiveCatalogPromotionCommand = new ArchiveCatalogPromotion('CATALOG_PROMOTION_CODE');

        $commandBus->dispatch($updateCatalogPromotionStateCommand)->willReturn(new Envelope($updateCatalogPromotionStateCommand))->shouldBeCalled();
        $commandBus->dispatch($disableCatalogPromotionCommand)->willReturn(new Envelope($disableCatalogPromotionCommand))->shouldBeCalled();
        $commandBus->dispatch($archiveCatalogPromotionCommand)->willReturn(new Envelope($archiveCatalogPromotionCommand))->shouldBeCalled();

        $this->dispatchCatalogPromotionArchival($catalogPromotion);
    }

    function it_dispatches_archive_catalog_promotion_command_on_disabled_catalog_promotion(
        MessageBusInterface $commandBus,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotion->getCode()->willReturn('CATALOG_PROMOTION_CODE');
        $catalogPromotion->isEnabled()->willReturn(false);

        $updateCatalogPromotionStateCommand = new UpdateCatalogPromotionState('CATALOG_PROMOTION_CODE');
        $disableCatalogPromotionCommand = new DisableCatalogPromotion('CATALOG_PROMOTION_CODE');
        $archiveCatalogPromotionCommand = new ArchiveCatalogPromotion('CATALOG_PROMOTION_CODE');

        $commandBus->dispatch($updateCatalogPromotionStateCommand)->willReturn(new Envelope($updateCatalogPromotionStateCommand))->shouldBeCalled();
        $commandBus->dispatch($disableCatalogPromotionCommand)->shouldNotBeCalled();
        $commandBus->dispatch($archiveCatalogPromotionCommand)->willReturn(new Envelope($archiveCatalogPromotionCommand))->shouldBeCalled();

        $this->dispatchCatalogPromotionArchival($catalogPromotion);
    }

    function it_dispatches_restore_catalog_promotion_command_on_catalog_promotion(
        MessageBusInterface $commandBus,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotion->getCode()->willReturn('CATALOG_PROMOTION_CODE');

        $restoreCatalogPromotionCommand = new RestoreCatalogPromotion('CATALOG_PROMOTION_CODE');

        $commandBus->dispatch($restoreCatalogPromotionCommand)->willReturn(new Envelope($restoreCatalogPromotionCommand))->shouldBeCalled();

        $this->dispatchCatalogPromotionRestoral($catalogPromotion);
    }
}
