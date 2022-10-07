<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Psr\Container\ContainerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\CreateResourceEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\ModelFactory;

final class CreateResourceSubscriber implements EventSubscriberInterface
{
    public function __construct(private ContainerInterface $factoryLocator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [CreateResourceEvent::class  => ['createResource', -10]];
    }

    public function createResource(CreateResourceEvent $event): void
    {
        $event->setResource(
            $this->getFactory($event->getFactory())::new()
                ->withAttributes($event->getAttributes())
                ->create())
        ;

        $event->stopPropagation();
    }

    private function getFactory(string $factory): ModelFactory
    {
        Assert::true($this->factoryLocator->has($factory), sprintf('Factory "%s" was not found.', $factory));

        return $this->factoryLocator->get($factory);
    }
}
