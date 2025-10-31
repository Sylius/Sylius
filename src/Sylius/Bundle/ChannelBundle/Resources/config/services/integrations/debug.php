<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.context.channel.fake_channel.persister', 'Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelPersister')
        ->args([service('sylius.context.channel.fake_channel.code_provider')])
        ->tag('kernel.event_listener', ['event' => 'kernel.response', 'method' => 'onKernelResponse', 'priority' => -8192]);

    $services->set('sylius.context.channel.fake_channel.code_provider', 'Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProvider')
        ->private();

    $services->alias('Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProviderInterface', 'sylius.context.channel.fake_channel.code_provider');

    $services->set('sylius.context.channel.fake_channel.context', 'Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelContext')
        ->private()
        ->args([
            service('sylius.context.channel.fake_channel.code_provider'),
            service('sylius.repository.channel'),
            service('request_stack'),
        ])
        ->tag('sylius.context.channel', ['priority' => 128]);
};
