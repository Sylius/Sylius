<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Psr\Container\ContainerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\ModelFactory;

final class FindOrCreateResourceSubscriber implements EventSubscriberInterface
{
    public function __construct(private ContainerInterface $factoryLocator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FindOrCreateResourceEvent::class  => ['findOrCreateResource', -30]];
    }

    public function findOrCreateResource(FindOrCreateResourceEvent $event): void
    {
        $event->setResource($this->getFactory($event->getFactory())::findOrCreate($event->getAttributes()));

        $event->stopPropagation();
    }

    private function getFactory(string $repository): ModelFactory
    {
        Assert::true($this->factoryLocator->has($repository), sprintf('Factory "%s" was not found.', $repository));

        return $this->factoryLocator->get($repository);
    }
}
