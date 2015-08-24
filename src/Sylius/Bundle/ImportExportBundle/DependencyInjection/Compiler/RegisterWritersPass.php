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
 * Registers all writers in profile registry service.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegisterWritersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.export.writer') || !$container->hasDefinition('sylius.registry.import.writer')) {
            return;
        }

        $exporterRegistry = $container->getDefinition('sylius.registry.export.writer');
        $taggedExportWriters = $container->findTaggedServiceIds('sylius.export.writer');

        $container->setParameter('sylius.export.writers', $this->registerWriters($taggedExportWriters, $exporterRegistry));

        $importerRegistry = $container->getDefinition('sylius.registry.import.writer');
        $taggedImportWriters = $container->findTaggedServiceIds('sylius.import.writer');

        $container->setParameter('sylius.import.writers', $this->registerWriters($taggedImportWriters, $importerRegistry));
    }

    /**
     * @param array      $taggedWriters
     * @param Definition $registry
     *
     * @return array
     */
    private function registerWriters(array $taggedWriters, $registry)
    {
        $writers = array();

        foreach ($taggedWriters as $id => $attributes) {
            $this->checkAttributeCorrectness($attributes);

            $name = $attributes[0]['writer'];
            $writers[$name] = $attributes[0]['label'];

            $registry->addMethodCall('register', array($name, new Reference($id)));
        }

        return $writers;
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
        if (!isset($attributes[0]['writer']) || !isset($attributes[0]['label'])) {
            throw new \InvalidArgumentException('Tagged writers needs to have `writer` and `label` attributes.');
        }
    }
}
