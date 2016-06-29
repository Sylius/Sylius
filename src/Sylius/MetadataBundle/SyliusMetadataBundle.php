<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\MetadataBundle;

use Sylius\MetadataBundle\DependencyInjection\Compiler\DynamicFormsChoicesMapCompilerPass;
use Sylius\MetadataBundle\DependencyInjection\Compiler\MetadataHierarchyProviderCompilerPass;
use Sylius\MetadataBundle\DependencyInjection\Compiler\MetadataRendererCompilerPass;
use Sylius\ResourceBundle\AbstractResourceBundle;
use Sylius\ResourceBundle\SyliusResourceBundle;
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
    public function getSupportedDrivers()
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\MetadataBundle\Model';
    }
}
