<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TranslationListenerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $driver = $container->getParameter('sylius_translation.driver');

        if ($driver == SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM) {
            $definition = $container->findDefinition('sylius.translatable.listener.locale');
            $definition->replaceArgument(0, new Reference('sylius.odm_translatable.listener'));
        }
    }
}
