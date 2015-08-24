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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all readers in profile registry service.
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
        $taggedExportReaders = $container->findTaggedServiceIds('sylius.export.reader');

        $container->setParameter('sylius.export.readers', $this->registerReaders($taggedExportReaders, $exporterRegistry));

        $importerRegistry = $container->getDefinition('sylius.registry.import.reader');
        $taggedImportReaders = $container->findTaggedServiceIds('sylius.import.reader');

        $container->setParameter('sylius.import.readers', $this->registerReaders($taggedImportReaders, $importerRegistry));
    }

    /**
     * @param array      $container
     * @param Definition $registry
     *
     * @return array
     */
    private function registerReaders(array $taggedReaders, $registry)
    {
        $readers = array();

        foreach ($taggedReaders as $id => $attributes) {
            $this->checkAttributeCorrectness($attributes);

            $name = $attributes[0]['reader'];
            $readers[$name] = $attributes[0]['label'];

            $registry->addMethodCall('register', array($name, new Reference($id)));
        }

        return $readers;
    }

    /**
     * @param array $attributes
     *
     * @return null
     *
     * @throws \InvalidArgumentException
     */
    private function checkAttributeCorrectness(array $attributes)
    {
        if (!isset($attributes[0]['reader']) || !isset($attributes[0]['label'])) {
            throw new \InvalidArgumentException('Tagged readers needs to have `reader` and `label` attributes.');
        }
    }
}
