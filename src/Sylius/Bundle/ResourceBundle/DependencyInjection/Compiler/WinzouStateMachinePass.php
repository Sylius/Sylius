<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Marks WinzouStateMachineBundle's "SM\Factory\FactoryInterface" service as public.
 *
 * @see https://github.com/winzou/StateMachineBundle/pull/44
 */
final class WinzouStateMachinePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        try {
            $container->findDefinition('SM\Factory\FactoryInterface')->setPublic(true);
        } catch (ServiceNotFoundException $exception) {
        }
    }
}
