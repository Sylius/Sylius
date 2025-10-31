<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('price_history/checkers.php');
    $container->import('price_history/command_dispatcher.php');
    $container->import('price_history/command_handler.php');
    $container->import('price_history/listeners.php');
    $container->import('price_history/logger.php');
    $container->import('price_history/processors.php');
    
};
