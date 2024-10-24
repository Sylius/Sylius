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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Listener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionStateChangedListenerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $messageBus): void
    {
        $this->beConstructedWith($messageBus);
    }

    function it_dispatches_update_state_command_of_catalog_promotion_that_has_just_been_created(
        MessageBusInterface $messageBus,
    ): void {
        $command = new UpdateCatalogPromotionState('WINTER_MUGS_SALE');
        $messageBus->dispatch($command)->willReturn(new Envelope($command))->shouldBeCalled();

        $this(new CatalogPromotionCreated('WINTER_MUGS_SALE'));
    }

    function it_dispatches_update_state_command_of_catalog_promotion_that_has_just_been_updated(
        MessageBusInterface $messageBus,
    ): void {
        $command = new UpdateCatalogPromotionState('WINTER_MUGS_SALE');
        $messageBus->dispatch($command)->willReturn(new Envelope($command))->shouldBeCalled();

        $this(new CatalogPromotionUpdated('WINTER_MUGS_SALE'));
    }

    function it_dispatches_update_state_command_of_catalog_promotion_that_has_just_been_ended(
        MessageBusInterface $messageBus,
    ): void {
        $command = new UpdateCatalogPromotionState('WINTER_MUGS_SALE');
        $messageBus->dispatch($command)->willReturn(new Envelope($command))->shouldBeCalled();

        $this(new CatalogPromotionEnded('WINTER_MUGS_SALE'));
    }
}
