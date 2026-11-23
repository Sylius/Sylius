<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\TaxonomyBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class SyliusTaxonomyExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load(sprintf('services/integrations/%s.php', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.php');
    }
}
