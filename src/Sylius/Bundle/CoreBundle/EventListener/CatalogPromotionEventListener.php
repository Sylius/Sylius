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

use Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class CatalogPromotionEventListener
{
    private MessageBusInterface $eventBus;

    private DelayStampCalculatorInterface $delayStampCalculator;

    public function __construct(MessageBusInterface $eventBus, DelayStampCalculatorInterface $delayStampCalculator)
    {
        $this->eventBus = $eventBus;
        $this->delayStampCalculator = $delayStampCalculator;
    }

    public function dispatchCatalogPromotionUpdatedEvent(GenericEvent $event): void
    {
        /** @var CatalogPromotionInterface $catalogPromotion */
        $catalogPromotion = $event->getSubject();
        Assert::isInstanceOf($catalogPromotion, CatalogPromotionInterface::class);

        $this->eventBus->dispatch(
            new CatalogPromotionUpdated($catalogPromotion->getCode()),
            [$this->delayStampCalculator->calculate(new \DateTime('now'), $catalogPromotion->getStartDate())]
        );

        if ($catalogPromotion->getEndDate() !== null) {
            $this->eventBus->dispatch(
                new CatalogPromotionEnded($catalogPromotion->getCode()),
                [$this->delayStampCalculator->calculate(new \DateTime('now'), $catalogPromotion->getEndDate())]
            );
        }
    }
}
