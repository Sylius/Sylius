<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\DatabaseDriverFactory;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Resource system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusResourceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('storage.xml');
        $loader->load('twig.xml');

        $classes = isset($config['resources']) ? $config['resources'] : array();

        foreach ($classes as $key => &$value) {
            if (isset($value['classes'])) {
                $value['classes'] = $this->getClassesConfig($container, $value);
            }
        }

        $container->setParameter('sylius.resource.settings', $config['settings']);

        $this->createResourceServices($classes, $container);

        if ($container->hasParameter('sylius.config.classes')) {
            $classes = array_merge($classes, $container->getParameter('sylius.config.classes'));
        }

        $container->setParameter('sylius.config.classes', $classes);
    }

    /**
     * Get classes configuration.
     *
     * @param ContainerBuilder  $container
     * @param array             $config
     *
     * @return array
     */
    protected function getClassesConfig(ContainerBuilder $container, array $config = null)
    {
        $classes = isset($config['classes']) ? $config['classes'] : array();

        if (isset($classes['controller']) && $this->isDefaultClass($container, $classes['controller'], Configuration::CLASS_CONTROLLER, Configuration::PARAMETER_CONTROLLER)) {
            $classes['controller'] = $container->getParameter(Configuration::PARAMETER_CONTROLLER);
        }

        if (isset($classes['repository'])) {
            if ($this->isDefaultClass($container, $classes['repository'], Configuration::CLASS_REPOSITORY_ORM, Configuration::PARAMETER_REPOSITORY_ORM)) {
                $classes['repository'] = $container->getParameter(Configuration::PARAMETER_REPOSITORY_ORM);
            } elseif ($this->isDefaultClass($container, $classes['repository'], Configuration::CLASS_REPOSITORY_ODM, Configuration::PARAMETER_REPOSITORY_ODM)) {
                $classes['repository'] = $container->getParameter(Configuration::PARAMETER_REPOSITORY_ODM);
            } elseif ($this->isDefaultClass($container, $classes['repository'], Configuration::CLASS_REPOSITORY_PHPCR, Configuration::PARAMETER_REPOSITORY_PHPCR)) {
                $classes['repository'] = $container->getParameter(Configuration::PARAMETER_REPOSITORY_PHPCR);
            }
        }

        return $classes;
    }

    /**
     * Check class is default and has parameter.
     *
     * @param ContainerBuilder  $container
     * @param string            $userClass
     * @param string            $defaultClass
     * @param string            $parameterName
     *
     * @return bool
     */
    private function isDefaultClass(ContainerBuilder $container, $userClass, $defaultClass, $parameterName)
    {
        if (($userClass === $defaultClass || $userClass === $parameterName)
            && $container->hasParameter($parameterName)) {
            return true;
        }

        return false;
    }

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    private function createResourceServices(array $configs, ContainerBuilder $container)
    {
        foreach ($configs as $name => $config) {
            list($prefix, $resourceName) = explode('.', $name);

            DatabaseDriverFactory::get(
                $config['driver'],
                $container,
                $prefix,
                $resourceName,
                isset($config['object_manager']) ? $config['object_manager'] : 'default',
                array_key_exists('templates', $config) ? $config['templates'] : null
            )->load($config['classes']);
        }
    }
}
