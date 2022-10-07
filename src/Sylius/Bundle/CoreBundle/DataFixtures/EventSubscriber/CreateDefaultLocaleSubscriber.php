<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\EventSubscriber;

use Psr\Container\ContainerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\CreateDefaultLocaleEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\CreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\ModelFactory;

final class CreateDefaultLocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(private LocaleFactoryInterface $localeFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [CreateDefaultLocaleEvent::class  => ['createDefaultLocale', -10]];
    }

    public function createDefaultLocale(CreateDefaultLocaleEvent $event): void
    {
        $event->setLocale(
            $this->localeFactory::new()
                ->withDefaultCode()
                ->create())
        ;

        $event->stopPropagation();
    }
}
