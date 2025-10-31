<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $parameters->set('sylius.repository.avatar_image.class', 'Sylius\Bundle\CoreBundle\Doctrine\ORM\AvatarImageRepository');
};
