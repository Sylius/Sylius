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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\DisableCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionRemovalAnnouncerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $commandBus): void
    {
        $this->beConstructedWith($commandBus);
    }

    function it_implements_catalog_promotion_removal_announcer_interface(): void
    {
        $this->shouldImplement(CatalogPromotionRemovalAnnouncerInterface::class);
    }

    function it_dispatches_remove_catalog_promotion_command_on_enabled_catalog_promotion(
        MessageBusInterface $commandBus,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotion->getCode()->willReturn('CATALOG_PROMOTION_CODE');
        $catalogPromotion->isEnabled()->willReturn(true);

        $updateCatalogPromotionStateCommand = new UpdateCatalogPromotionState('CATALOG_PROMOTION_CODE');
        $disableCatalogPromotionCommand = new DisableCatalogPromotion('CATALOG_PROMOTION_CODE');
        $removeCatalogPromotionCommand = new RemoveCatalogPromotion('CATALOG_PROMOTION_CODE');

        $commandBus->dispatch($updateCatalogPromotionStateCommand)->willReturn(new Envelope($updateCatalogPromotionStateCommand))->shouldBeCalled();
        $commandBus->dispatch($disableCatalogPromotionCommand)->willReturn(new Envelope($disableCatalogPromotionCommand))->shouldBeCalled();
        $commandBus->dispatch($removeCatalogPromotionCommand)->willReturn(new Envelope($removeCatalogPromotionCommand))->shouldBeCalled();

        $this->dispatchCatalogPromotionRemoval($catalogPromotion);
    }

    function it_dispatches_remove_catalog_promotion_command_on_disabled_catalog_promotion(
        MessageBusInterface $commandBus,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotion->getCode()->willReturn('CATALOG_PROMOTION_CODE');
        $catalogPromotion->isEnabled()->willReturn(false);

        $updateCatalogPromotionStateCommand = new UpdateCatalogPromotionState('CATALOG_PROMOTION_CODE');
        $disableCatalogPromotionCommand = new DisableCatalogPromotion('CATALOG_PROMOTION_CODE');
        $removeCatalogPromotionCommand = new RemoveCatalogPromotion('CATALOG_PROMOTION_CODE');

        $commandBus->dispatch($updateCatalogPromotionStateCommand)->willReturn(new Envelope($updateCatalogPromotionStateCommand))->shouldBeCalled();
        $commandBus->dispatch($disableCatalogPromotionCommand)->shouldNotBeCalled();
        $commandBus->dispatch($removeCatalogPromotionCommand)->willReturn(new Envelope($removeCatalogPromotionCommand))->shouldBeCalled();

        $this->dispatchCatalogPromotionRemoval($catalogPromotion);
    }
}
