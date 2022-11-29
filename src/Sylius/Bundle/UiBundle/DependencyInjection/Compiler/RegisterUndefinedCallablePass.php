<?php

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\DependencyInjection\Compiler;

use Sylius\Bundle\UiBundle\Twig\UndefinedCallableHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterUndefinedCallablePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('twig')) {
            return;
        }

        $container->getDefinition('twig')
            ->addMethodCall('registerUndefinedFunctionCallback', [[new Reference(UndefinedCallableHandler::class), 'onUndefinedFunction']])
            ->addMethodCall('registerUndefinedFilterCallback', [[new Reference(UndefinedCallableHandler::class), 'onUndefinedFilter']])
        ;
    }
}
