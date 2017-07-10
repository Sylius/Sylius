<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\DependencyInjection\Compiler;

use Sylius\Bundle\UserBundle\Authentication\DefaultAuthenticationSuccessHandler as SyliusDefaultAuthenticationSuccessHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler as SymfonyDefaultAuthenticationSuccessHandler;

/**
 * @see SyliusDefaultAuthenticationSuccessHandler
 *
 * @internal
 */
final class SymfonyCompatibilityPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $definition) {
            if ($definition->getClass() === SymfonyDefaultAuthenticationSuccessHandler::class) {
                $definition->setClass(SyliusDefaultAuthenticationSuccessHandler::class);
            }
        }
    }
}
