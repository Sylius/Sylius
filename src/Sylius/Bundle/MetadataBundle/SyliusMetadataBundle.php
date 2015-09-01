<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\MetadataBundle\DependencyInjection\Compiler\DynamicFormsChoicesMapCompilerPass;
use Sylius\Bundle\MetadataBundle\DependencyInjection\Compiler\MetadataHierarchyProviderCompilerPass;
use Sylius\Bundle\MetadataBundle\DependencyInjection\Compiler\MetadataRendererCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SyliusMetadataBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MetadataRendererCompilerPass());
        $container->addCompilerPass(new MetadataHierarchyProviderCompilerPass());
        $container->addCompilerPass(new DynamicFormsChoicesMapCompilerPass());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return [
            'Sylius\Component\Metadata\Model\RootMetadataInterface' => 'sylius.model.metadata.class',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Metadata\Model';
    }
}
