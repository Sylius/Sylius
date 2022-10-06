<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateTaxonByQueryStringEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FindOrCreateTaxonSubscriber implements EventSubscriberInterface
{
    public function __construct(private TaxonFactoryInterface $taxonFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateTaxonByQueryStringEvent::class  => ['findOrCreateTaxon', -10]];
    }

    public function findOrCreateTaxon(FindOrCreateTaxonByQueryStringEvent $event): void
    {
        $event->setTaxon($this->taxonFactory::findOrCreate(['code' => $event->getQueryString()]));

        $event->stopPropagation();
    }
}
