<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateProductByCodeEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FindOrCreateProductByCodeSubscriber implements EventSubscriberInterface
{
    public function __construct(private ProductFactoryInterface $productFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateProductByCodeEvent::class  => ['findOrCreateProduct', -10]];
    }

    public function findOrCreateProduct(FindOrCreateProductByCodeEvent $event): void
    {
        $event->setProduct($this->productFactory::findOrCreate(['code' => $event->getCode()]));
    }
}
