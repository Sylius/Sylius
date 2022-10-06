<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateTaxCategoryByQueryStringEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxCategoryFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FindOrCreateTaxCategorySubscriber implements EventSubscriberInterface
{
    public function __construct(private TaxCategoryFactoryInterface $taxCategoryFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateTaxCategoryByQueryStringEvent::class  => ['findOrCreateTaxCategory', -10]];
    }

    public function findOrCreateTaxCategory(FindOrCreateTaxCategoryByQueryStringEvent $event): void
    {
        $event->setTaxCategory($this->taxCategoryFactory::findOrCreate(['code' => $event->getQueryString()]));

        $event->stopPropagation();
    }
}
