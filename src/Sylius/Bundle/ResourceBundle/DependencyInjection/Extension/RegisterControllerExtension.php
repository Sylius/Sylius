<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Extension;

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DatabaseDriverFactory;
use Sylius\Component\Resource\Exception\Driver\InvalidDriverException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterControllerExtension 
{
    /**
     * {@inheritdoc}
     */
    public function isSupported($configurationure)
    {
        return $configurationure & AbstractResourceExtension::CONFIGURE_DATABASE;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ContainerBuilder $container, array $configuration, array $context = array())
    {
        $bundle = str_replace(array('Extension', 'DependencyInjection\\'), array('Bundle', ''), get_class($this));
        $driver = $configuration['driver'];
        $manager = isset($configuration['object_manager']) ? $configuration['object_manager'] : 'default';

        if (!in_array($driver, call_user_func(array($bundle, 'getSupportedDrivers')))) {
            throw new InvalidDriverException($driver, basename($bundle));
        }

        $this->loadServiceDefinitions(array(sprintf('driver/%s', $driver)), $context['laoder']);

        $container->setParameter(sprintf('%s.driver', $this->getAlias()), $driver);
        $container->setParameter(sprintf('%s.driver.%s', $this->getAlias(), $driver), true);
        $container->setParameter(sprintf('%s.object_manager', $this->getAlias()), $manager);

        foreach ($configuration['classes'] as $model => $classes) {
            if (array_key_exists('model', $classes)) {
                DatabaseDriverFactory::get(
                    $container,
                    $context['app_name'],
                    $model,
                    $manager,
                    $driver,
                    isset($configuration['templates'][$model]) ? $configuration['templates'][$model] : null
                )->load($classes);
            }
        }
    }
}