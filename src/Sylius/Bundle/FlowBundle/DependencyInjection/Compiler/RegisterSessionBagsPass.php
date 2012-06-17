<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

/**
 * Registers a namespaced attribute bag for all processes.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class RegisterSessionBagsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $session = $container->getDefinition('session');
        $session->addMethodCall('registerBag', array(new Reference('sylius_flow.storage.session.bag')));
    }
}
