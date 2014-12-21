<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Aram Alipoor
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OverrideDateTypeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('form.type.date');

        $definition->setClass('Sylius\Bundle\LocaleBundle\Form\Type\DateType');
        $definition->addArgument(new Reference('sylius.templating.helper.locale'));
    }
}