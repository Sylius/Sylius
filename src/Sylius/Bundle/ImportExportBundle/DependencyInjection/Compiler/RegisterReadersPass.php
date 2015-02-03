<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all readers in export profile registry service.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegisterReadersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.export.reader') || !$container->hasDefinition('sylius.registry.import.reader')) {
            return;
        }

        $exporterRegistry = $container->getDefinition('sylius.registry.export.reader');
        $importerRegistry = $container->getDefinition('sylius.registry.import.reader');

        $exportReaders = array();
        $importReaders = array();

        foreach ($container->findTaggedServiceIds('sylius.export.reader') as $id => $attributes) {
            if (!isset($attributes[0]['reader']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged readers needs to have `reader` and `label` attributes.');
            }

            $name = $attributes[0]['reader'];
            $exportReaders[$name] = $attributes[0]['label'];

            $exporterRegistry->addMethodCall('register', array($name, new Reference($id)));
        }
        $container->setParameter('sylius.export.readers', $exportReaders);

        foreach ($container->findTaggedServiceIds('sylius.import.reader') as $id => $attributes) {
            if (!isset($attributes[0]['reader']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged readers needs to have `reader` and `label` attributes.');
            }

            $name = $attributes[0]['reader'];
            $importReaders[$name] = $attributes[0]['label'];

            $importerRegistry->addMethodCall('register', array($name, new Reference($id)));
        }
        $container->setParameter('sylius.import.readers', $importReaders);
    }
}
