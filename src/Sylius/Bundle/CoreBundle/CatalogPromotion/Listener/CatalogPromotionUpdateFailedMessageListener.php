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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Listener;

use Sylius\Component\Promotion\Event\CatalogPromotionFailed;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionUpdateFailedMessageListener
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event)
    {
        if (!$this->isWorkerMessageValid($event)) {
            return;
        }

        $envelope = $event->getEnvelope();
        $message = $envelope->getMessage();

        $this->messageBus->dispatch(new CatalogPromotionFailed($message->code));
    }

    private function isWorkerMessageValid(WorkerMessageFailedEvent $event): bool
    {
        return
            $event->willRetry() === false &&
            $event->getEnvelope()->getMessage() instanceof CatalogPromotionUpdated
        ;
    }
}
