<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * Modifies resource services after initialization.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container
            ->getDefinition('sylius.form.type.province_choice')
            ->setArguments(array(
                new Reference('sylius.repository.province')
            ))
        ;

        $container
            ->getDefinition('sylius.form.type.province_choice')
            ->setArguments(array(
                new Reference('sylius.repository.province')
            ))
        ;

        $container
            ->getDefinition('sylius.form.type.address')
            ->addArgument(new Reference('sylius.form.listener.address'))
        ;

        $container
            ->getDefinition('sylius.form.type.zone')
            ->addArgument(new Parameter('sylius.scope.zone'))
        ;
    }
}
