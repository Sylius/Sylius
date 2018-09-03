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
 * Marks WinzouStateMachineBundle's services as public for compatibility with both Symfony 3.4 and 4.0+.
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
        foreach (['sm.factory', 'sm.callback_factory', 'sm.callback.cascade_transition'] as $id) {
            try {
                $container->findDefinition($id)->setPublic(true);
            } catch (ServiceNotFoundException $exception) {
            }
        }
    }
}
