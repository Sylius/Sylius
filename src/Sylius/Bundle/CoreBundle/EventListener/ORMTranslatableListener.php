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
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Resource\Provider\LocaleProviderInterface;
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

        /** @var LocaleProviderInterface $localeProvider */
        $localeProvider = $this->container->get('sylius_resource.translation.locale_provider');

        try {
            $entity->setCurrentLocale($localeContext->getLocaleCode());
        } catch (LocaleNotFoundException $exception) {
            $entity->setCurrentLocale($localeProvider->getDefaultLocaleCode());
        }
        $entity->setFallbackLocale($localeProvider->getDefaultLocaleCode());
    }
}
