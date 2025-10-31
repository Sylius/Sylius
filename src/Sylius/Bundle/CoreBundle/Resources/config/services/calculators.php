<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.calculator.delay_stamp', 'Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculator');

    $services->alias('Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface', 'sylius.calculator.delay_stamp');
};
