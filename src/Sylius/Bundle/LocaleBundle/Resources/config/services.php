<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageChecker;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Bundle\LocaleBundle\Context\RequestHeaderBasedLocaleContext;
use Sylius\Bundle\LocaleBundle\Doctrine\EventListener\LocaleModificationListener;
use Sylius\Bundle\LocaleBundle\Form\DataTransformer\LocaleToCodeTransformer;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleType;
use Sylius\Bundle\LocaleBundle\Listener\RequestLocaleSetter;
use Sylius\Bundle\LocaleBundle\Twig\LocaleExtension;
use Sylius\Component\Locale\Context\CompositeLocaleContext;
use Sylius\Component\Locale\Context\ImmutableLocaleContext;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Converter\LocaleConverter;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;
use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Locale\Provider\CachedLocaleCollectionProvider;
use Sylius\Component\Locale\Provider\LocaleCollectionProvider;
use Sylius\Component\Locale\Provider\LocaleProvider;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.form.type.locale.validation_groups', ['sylius']);

    $services->set('sylius.form.type.locale', LocaleType::class)
        ->args([
            '%sylius.model.locale.class%',
            '%sylius.form.type.locale.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.data_transformer.locale_to_code', LocaleToCodeTransformer::class)
        ->args([service('sylius.provider.locale_collection')]);

    $services->set('sylius.form.type.locale_choice', LocaleChoiceType::class)
        ->args([service('sylius.repository.locale')])
        ->tag('form.type');

    $services->set('sylius.context.locale', ImmutableLocaleContext::class)
        ->public()
        ->args(['%sylius_locale.locale%']);

    $services->alias(LocaleContextInterface::class, 'sylius.context.locale');

    $services->set('sylius.context.locale.composite', CompositeLocaleContext::class)
        ->private()
        ->decorate('sylius.context.locale', null, 256);

    $services->set('sylius.context.locale.request_header_based', RequestHeaderBasedLocaleContext::class)
        ->args([
            service('request_stack'),
            service('sylius.provider.locale'),
        ])
        ->tag('sylius.context.locale', ['priority' => 32]);

    $services->set('sylius.provider.locale_collection', LocaleCollectionProvider::class)
        ->args([service('sylius.repository.locale')]);

    $services->set('sylius.provider.locale_collection.cached', CachedLocaleCollectionProvider::class)
        ->decorate('sylius.provider.locale_collection')
        ->args([
            service('.inner'),
            service('cache.app'),
        ]);

    $services->set('sylius.provider.locale', LocaleProvider::class)
        ->args([
            service('sylius.provider.locale_collection'),
            '%sylius_locale.locale%',
        ]);

    $services->alias(LocaleProviderInterface::class, 'sylius.provider.locale');

    $services->set('sylius.converter.locale', LocaleConverter::class);

    $services->alias(LocaleConverterInterface::class, 'sylius.converter.locale');

    $services->set('sylius.listener.request_locale_setter', RequestLocaleSetter::class)
        ->args([
            service('sylius.context.locale'),
            service('sylius.provider.locale'),
        ])
        ->tag('kernel.event_listener', ['event' => 'kernel.request', 'priority' => 4]);

    $services->set('sylius.twig.extension.locale', LocaleExtension::class)
        ->args([
            service('sylius.converter.locale'),
            service('sylius.context.locale'),
        ])
        ->tag('twig.extension');

    $services->set('sylius.checker.locale_usage', LocaleUsageChecker::class)
        ->args([
            service('sylius.repository.locale'),
            service('sylius.resource_registry'),
            service('doctrine.orm.entity_manager'),
        ]);

    $services->alias(LocaleUsageCheckerInterface::class, 'sylius.checker.locale_usage');

    $services->set('sylius.doctrine.listener.locale_modification', LocaleModificationListener::class)
        ->args([service('cache.app')])
        ->tag('doctrine.orm.entity_listener', ['event' => 'postPersist', 'entity' => Locale::class, 'method' => 'invalidateCachedLocales'])
        ->tag('doctrine.orm.entity_listener', ['event' => 'postUpdate', 'entity' => Locale::class, 'method' => 'invalidateCachedLocales'])
        ->tag('doctrine.orm.entity_listener', ['event' => 'postRemove', 'entity' => Locale::class, 'method' => 'invalidateCachedLocales']);
};
