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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class CatalogPromotionEventListener
{
    private MessageBusInterface $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function dispatchCatalogPromotionUpdatedEvent(GenericEvent $event): void
    {
        $catalogPromotion = $event->getSubject();
        Assert::isInstanceOf($catalogPromotion, CatalogPromotionInterface::class);

        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }
}
