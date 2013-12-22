<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\BaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SyliusInstallerExtension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configDir = __DIR__.'/../Resources/config';

        list($config) = $this->configure($config, new Configuration(), $container);

        foreach ($config['classes'] as $model => $classes) {
            foreach ($classes as $service => $class) {
                $container->setParameter(sprintf('sylius.%s.%s.class', $service, $model), $class);
            }
        }
    }
}
