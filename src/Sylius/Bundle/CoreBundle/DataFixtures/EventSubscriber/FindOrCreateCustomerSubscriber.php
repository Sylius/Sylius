<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateChannelByQueryStringEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateCustomerByQueryStringEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FindOrCreateCustomerSubscriber implements EventSubscriberInterface
{
    public function __construct(private CustomerFactoryInterface $customerFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateCustomerByQueryStringEvent::class  => ['findOrCreateCustomer', -10]];
    }

    public function findOrCreateCustomer(FindOrCreateCustomerByQueryStringEvent $event): void
    {
        $event->setCustomer($this->customerFactory::findOrCreate(['email' => $event->getQueryString()]));

        $event->stopPropagation();
    }
}
