<?php

declare(strict_types=1);

use AppBundle\Service\FirstAutowiredService;
use AppBundle\Service\SecondAutowiredService;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

if (method_exists($container, 'registerAliasForArgument')) {
    $container->autowire(FirstAutowiredService::class)->setPublic(true);
    $container->autowire(SecondAutowiredService::class)->setPublic(true);
} else {
    $container->setDefinition(FirstAutowiredService::class, (new Definition(FirstAutowiredService::class, [
        new Reference('app.factory.book'),
        new Reference('app.repository.book'),
        new Reference('app.manager.book'),
    ]))->setPublic(true));

    $container->setDefinition(SecondAutowiredService::class, (new Definition(SecondAutowiredService::class, [
        new Reference('app.factory.book'),
        new Reference('app.repository.book'),
        new Reference('app.manager.book'),
    ]))->setPublic(true));
}
