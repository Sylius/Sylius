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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionFailed;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionUpdateFailedMessageListenerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $messageBus): void
    {
        $this->beConstructedWith($messageBus);
    }

    function it_dispatches_catalog_promotion_failed_for_failed_worker_message(
        MessageBusInterface $messageBus,
        CatalogPromotionFailed $catalogPromotionFailed,
    ): void {
        $failedEvent = new WorkerMessageFailedEvent(
            new Envelope(
                new CatalogPromotionUpdated('code'),
            ),
            'receiver',
            new \Exception('exception'),
        );

        $messageBus->dispatch(Argument::that(
            function ($object): bool {
                return
                    $object->code === 'code' &&
                    $object instanceof CatalogPromotionFailed
                ;
            },
        ))->willReturn(new Envelope($catalogPromotionFailed))->shouldBeCalled();

        $this->onMessageFailed($failedEvent);
    }

    function it_does_nothing_for_failed_messages_other_than_catalog_promotion_updated(
        MessageBusInterface $messageBus,
    ): void {
        $failedEvent = new WorkerMessageFailedEvent(
            new Envelope(
                new CatalogPromotionEnded('code'),
            ),
            'receiver',
            new \Exception('exception'),
        );

        $messageBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->onMessageFailed($failedEvent);
    }

    function it_does_nothing_for_failed_messages_that_can_be_retried_by_messenger(
        MessageBusInterface $messageBus,
    ): void {
        $failedEvent = new WorkerMessageFailedEvent(
            new Envelope(
                new CatalogPromotionEnded('code'),
            ),
            'receiver',
            new \Exception('exception'),
        );

        $failedEvent->setForRetry();

        $messageBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->onMessageFailed($failedEvent);
    }
}
