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

use Sylius\Bundle\ChannelBundle\Collector\ChannelCollector;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Component\Channel\Checker\ChannelDeletionChecker;
use Sylius\Component\Channel\Checker\ChannelDeletionCheckerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\CompositeChannelContext;
use Sylius\Component\Channel\Context\RequestBased\ChannelContext;
use Sylius\Component\Channel\Context\RequestBased\CompositeRequestResolver;
use Sylius\Component\Channel\Context\RequestBased\HostnameBasedRequestResolver;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Channel\Context\SingleChannelContext;
use Sylius\Component\Channel\Factory\ChannelFactory;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;

return static function (ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $services = $container->services();
    $parameters->set('sylius.channel.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.channel.validation_groups', ['sylius']);

    $services
        ->set('sylius.custom_factory.channel', ChannelFactory::class)
        ->decorate('sylius.factory.channel', null, 256)
        ->args([service('sylius.custom_factory.channel.inner')])
    ;
    $services->alias(ChannelFactoryInterface::class, 'sylius.custom_factory.channel');

    $services
        ->set('sylius.form.type.channel', ChannelType::class)
        ->args([
            '%sylius.model.channel.class%',
            '%sylius.form.type.channel.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.channel_choice', ChannelChoiceType::class)
        ->args([service('sylius.repository.channel')])
        ->tag('form.type')
    ;

    $services->set('sylius.context.channel.composite', CompositeChannelContext::class);

    $services->alias(ChannelContextInterface::class, 'sylius.context.channel');

    $services
        ->set('sylius.context.channel.request_based', ChannelContext::class)
        ->args([
            service('sylius.context.channel.request_based.resolver'),
            service('request_stack'),
        ])
        ->tag('sylius.context.channel')
    ;

    $services
        ->set('sylius.context.channel.single_channel', SingleChannelContext::class)
        ->args([service('sylius.repository.channel')])
        ->tag('sylius.context.channel', ['priority' => -128])
    ;

    $services->set('sylius.context.channel.request_based.resolver.composite', CompositeRequestResolver::class);
    $services->alias(RequestResolverInterface::class, 'sylius.context.channel.request_based.resolver.composite');

    $services
        ->set('sylius.context.channel.request_based.resolver.hostname_based', HostnameBasedRequestResolver::class)
        ->args([service('sylius.repository.channel')])
        ->tag('sylius.context.channel.request_based.resolver')
    ;

    $services
        ->set('sylius.collector.channel', ChannelCollector::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius.context.channel'),
            false,
        ])
        ->tag('data_collector', ['template' => '@SyliusChannel/Collector/channel.html.twig', 'id' => 'sylius.collector.channel'])
    ;

    $services
        ->set('sylius.checker.channel_deletion', ChannelDeletionChecker::class)
        ->args([service('sylius.repository.channel')])
    ;
    $services->alias(ChannelDeletionCheckerInterface::class, 'sylius.checker.channel_deletion');
};
