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
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ClassMapperExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSupported($configure)
    {
        return $configure & AbstractResourceExtension::CONFIGURE_PARAMETERS;;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ContainerBuilder $container, array $configuration, array $context = array())
    {
        if (isset($configuration['classes'])) {
            return;
        }
        
        foreach ($configuration['classes'] as $model => $serviceClasses) {
            foreach ($serviceClasses as $service => $class) {
                if ('form' === $service) {
                    if (!is_array($class)) {
                        $class = array(AbstractResourceExtension::DEFAULT_KEY => $class);
                    }
                    $service = 'form.type';
                    foreach ($class as $suffix => $subClass) {
                        $model .= $suffix === AbstractResourceExtension::DEFAULT_KEY ? '' : sprintf('_%s', $suffix);
                        $this->addParameterToContainer($container, $context['app_name'], $service, $model, $class);
                    }
                }

                $this->addParameterToContainer($container, $context['app_name'], $service, $model, $class);
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param $appName
     * @param $service
     * @param $model
     * @param $value
     */
    protected function addParameterToContainer(ContainerBuilder $container, $appName, $service, $model, $value)
    {
        $key = sprintf(
            '%s.%s.%s.class',
            $appName,
            $service,
            $model
        );

        $container->setParameter($key, $value);
    }
}