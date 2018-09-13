<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\DependencyInjection;

use Sylius\Bundle\GridBundle\SyliusGridBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SyliusGridExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        $container->setParameter('sylius.grid.templates.action', $config['templates']['action']);
        $container->setParameter('sylius.grid.templates.bulk_action', $config['templates']['bulk_action']);
        $container->setParameter('sylius.grid.templates.filter', $config['templates']['filter']);
        $container->setParameter('sylius.grids_definitions', $config['grids']);

        $container->setAlias('sylius.grid.renderer', 'sylius.grid.renderer.twig');
        $container->setAlias('sylius.grid.bulk_action_renderer', 'sylius.grid.bulk_action_renderer.twig');
        $container->setAlias('sylius.grid.data_extractor', 'sylius.grid.data_extractor.property_access');

        foreach ($config['drivers'] as $enabledDriver) {
            if ($enabledDriver === SyliusGridBundle::DRIVER_DOCTRINE_PHPCR_ODM) {
                @trigger_error(sprintf(
                    'The "%s" driver is deprecated in Sylius 1.3. Doctrine PHPCR will no longer be supported in Sylius 2.0.',
                    SyliusGridBundle::DRIVER_DOCTRINE_PHPCR_ODM
                ), E_USER_DEPRECATED);
            }

            $loader->load(sprintf('services/integrations/%s.xml', $enabledDriver));
        }
    }
}
