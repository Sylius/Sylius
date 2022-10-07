<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\CreatePromotionRuleEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\CreateShopBillingDataEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopBillingDataFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CreatePromotionRuleSubscriber implements EventSubscriberInterface
{
    public function __construct(private ShopBillingDataFactoryInterface $shopBillingDataFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [CreatePromotionRuleEvent::class  => ['createShopBillingData', -10]];
    }

    public function createShopBillingData(CreateShopBillingDataEvent $event): void
    {
        $event->setShopBillingData($this->shopBillingDataFactory::new()->withAttributes($event->getData())->create());

        $event->stopPropagation();
    }
}
