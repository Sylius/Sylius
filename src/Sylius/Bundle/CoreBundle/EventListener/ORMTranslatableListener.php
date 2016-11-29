<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ORMTranslatableListener implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
            Events::postLoad,
        ];
    }


    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof TranslatableInterface) {
            return;
        }

        /** @var LocaleContextInterface $localeContext */
        $localeContext = $this->container->get('sylius.context.locale');

        /** @var TranslationLocaleProviderInterface $localeProvider */
        $localeProvider = $this->container->get('sylius.translation_locale_provider');

        try {
            $entity->setCurrentLocale($localeContext->getLocaleCode());
        } catch (LocaleNotFoundException $exception) {
            $entity->setCurrentLocale($localeProvider->getDefaultLocaleCode());
        }
        $entity->setFallbackLocale($localeProvider->getDefaultLocaleCode());
    }
}
