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
        $definition = $container->findDefinition('sylius.translatable.listener.locale');
        $reference = null;

        switch ($driver) {
            case SyliusResourceBundle::DRIVER_DOCTRINE_ORM:
                $reference = 'sylius.orm_translatable_listener';
            break;

            case SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM:
                $reference = 'sylius.mongodb_odm_translatable_listener';
            break;
        }

        if (null === $reference) {
            throw new \InvalidArgumentException(sprintf('Translations do not support "%s" driver.', $driver));
        }

        $definition->replaceArgument(0, new Reference($reference));
    }
}
