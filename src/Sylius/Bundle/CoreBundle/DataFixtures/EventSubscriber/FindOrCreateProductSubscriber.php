<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateProductByStringEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FindOrCreateProductSubscriber implements EventSubscriberInterface
{
    public function __construct(private ProductFactoryInterface $productFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateProductByStringEvent::class  => ['findOrCreateProduct', -10]];
    }

    public function findOrCreateProduct(FindOrCreateProductByStringEvent $event): void
    {
        $event->setProduct($this->productFactory::findOrCreate(['code' => $event->getCode()]));

        $event->stopPropagation();
    }
}
