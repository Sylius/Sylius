<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.channel.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.channel.validation_groups', ['sylius']);

    $services->set('sylius.custom_factory.channel', 'Sylius\Component\Channel\Factory\ChannelFactory')
        ->private()
        ->decorate('sylius.factory.channel', null, 256)
        ->args([service('sylius.custom_factory.channel.inner')]);

    $services->alias('Sylius\Component\Channel\Factory\ChannelFactoryInterface', 'sylius.custom_factory.channel');

    $services->set('sylius.form.type.channel', 'Sylius\Bundle\ChannelBundle\Form\Type\ChannelType')
        ->args([
            '%sylius.model.channel.class%',
            '%sylius.form.type.channel.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.channel_choice', 'Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType')
        ->args([service('sylius.repository.channel')])
        ->tag('form.type');

    $services->set('sylius.context.channel.composite', 'Sylius\Component\Channel\Context\CompositeChannelContext');

    $services->alias('Sylius\Component\Channel\Context\ChannelContextInterface', 'sylius.context.channel');

    $services->set('sylius.context.channel.request_based', 'Sylius\Component\Channel\Context\RequestBased\ChannelContext')
        ->args([
            service('sylius.context.channel.request_based.resolver'),
            service('request_stack'),
        ])
        ->tag('sylius.context.channel');

    $services->set('sylius.context.channel.single_channel', 'Sylius\Component\Channel\Context\SingleChannelContext')
        ->args([service('sylius.repository.channel')])
        ->tag('sylius.context.channel', ['priority' => -128]);

    $services->set('sylius.context.channel.request_based.resolver.composite', 'Sylius\Component\Channel\Context\RequestBased\CompositeRequestResolver');

    $services->alias('Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface', 'sylius.context.channel.request_based.resolver.composite');

    $services->set('sylius.context.channel.request_based.resolver.hostname_based', 'Sylius\Component\Channel\Context\RequestBased\HostnameBasedRequestResolver')
        ->args([service('sylius.repository.channel')])
        ->tag('sylius.context.channel.request_based.resolver');

    $services->set('sylius.collector.channel', 'Sylius\Bundle\ChannelBundle\Collector\ChannelCollector')
        ->args([
            service('sylius.repository.channel'),
            service('sylius.context.channel'),
            false,
        ])
        ->tag('data_collector', ['template' => '@SyliusChannel/Collector/channel.html.twig', 'id' => 'sylius.collector.channel']);

    $services->set('sylius.checker.channel_deletion', 'Sylius\Component\Channel\Checker\ChannelDeletionChecker')
        ->args([service('sylius.repository.channel')]);

    $services->alias('Sylius\Component\Channel\Checker\ChannelDeletionCheckerInterface', 'sylius.checker.channel_deletion');
};
