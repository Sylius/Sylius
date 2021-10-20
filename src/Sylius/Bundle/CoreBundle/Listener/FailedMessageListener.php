<?php


namespace Sylius\Bundle\CoreBundle\Listener;


use Sylius\Component\Promotion\Event\CatalogPromotionFailed;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class FailedMessageListener implements EventSubscriberInterface
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event)
    {
        $envelope = $event->getEnvelope();
        $message = $envelope->getMessage();

        $this->messageBus->dispatch(new CatalogPromotionFailed($message->getMessage()->code));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => ['onMessageFailed', 150],
        ];
    }
}
