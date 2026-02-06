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

use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProvider;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProviderInterface;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelContext;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelPersister;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.context.channel.fake_channel.persister', FakeChannelPersister::class)
        ->args([service('sylius.context.channel.fake_channel.code_provider')])
        ->tag('kernel.event_listener', ['event' => 'kernel.response', 'method' => 'onKernelResponse', 'priority' => -8192]);

    $services->set('sylius.context.channel.fake_channel.code_provider', FakeChannelCodeProvider::class);

    $services->alias(FakeChannelCodeProviderInterface::class, 'sylius.context.channel.fake_channel.code_provider');

    $services->set('sylius.context.channel.fake_channel.context', FakeChannelContext::class)
        ->args([
            service('sylius.context.channel.fake_channel.code_provider'),
            service('sylius.repository.channel'),
            service('request_stack'),
        ])
        ->tag('sylius.context.channel', ['priority' => 128]);
};
