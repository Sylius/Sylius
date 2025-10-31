<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $parameters->set('sylius.repository.channel.class', 'Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository');
};
