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

namespace Sylius\Bundle\ApiBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionEventSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $eventBus;

    private DelayStampCalculatorInterface $delayStampCalculator;

    public function __construct(MessageBusInterface $eventBus, DelayStampCalculatorInterface $delayStampCalculator)
    {
        $this->eventBus = $eventBus;
        $this->delayStampCalculator = $delayStampCalculator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['postWrite', EventPriorities::POST_WRITE],
        ];
    }

    public function postWrite(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();

        if (!$entity instanceof CatalogPromotionInterface) {
            return;
        }

        $method = $event->getRequest()->getMethod();

        if (in_array($method, [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH], true)) {
            $this->eventBus->dispatch(
                new CatalogPromotionUpdated($entity->getCode()),
                [$this->delayStampCalculator->calculate(new \DateTime('now'), $entity->getStartDate())]
            );

            if ($entity->getEndDate() !== null) {
                $this->eventBus->dispatch(
                    new CatalogPromotionEnded($entity->getCode()),
                    [$this->delayStampCalculator->calculate(new \DateTime('now'), $entity->getEndDate())]
                );
            }
        }
    }
}
