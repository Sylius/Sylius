<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.requirements.installer.sylius', 'Sylius\Bundle\CoreBundle\Installer\Requirement\SyliusRequirements')
        ->args([['' => inline_service('Sylius\Bundle\CoreBundle\Installer\Requirement\SettingsRequirements')
            ->args([service('translator')]), '' => inline_service('Sylius\Bundle\CoreBundle\Installer\Requirement\ExtensionsRequirements')
            ->args([service('translator')]), '' => inline_service('Sylius\Bundle\CoreBundle\Installer\Requirement\FilesystemRequirements')
            ->args([
                service('translator'),
                '%kernel.cache_dir%',
                '%kernel.logs_dir%',
            ])]]);
};
