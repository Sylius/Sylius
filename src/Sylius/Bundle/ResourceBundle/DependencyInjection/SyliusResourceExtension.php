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
    protected $classesToCompile = array(
        'Sylius\Bundle\ResourceBundle\Controller\Configuration',
        'Sylius\Bundle\ResourceBundle\Controller\DomainManager',
        'Sylius\Bundle\ResourceBundle\Controller\FlashHelper',
        'Sylius\Bundle\ResourceBundle\Controller\ParametersParser',
        'Sylius\Bundle\ResourceBundle\Controller\RedirectHandler',
        'Sylius\Bundle\ResourceBundle\Controller\ResourceController',
        'Sylius\Bundle\ResourceBundle\Controller\ResourceResolver',

        'Sylius\Bundle\ResourceBundle\Event\ResourceEvent',

        'Sylius\Component\Resource\Model\SoftDeletableInterface',
        'Sylius\Component\Resource\Model\TimestampableInterface',
        'Sylius\Component\Resource\Repository\RepositoryInterface',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('twig.xml');

        $classes = isset($config['resources']) ? $config['resources'] : array();

        $container->setParameter('sylius.resource.settings', $config['settings']);

        $this->createResourceServices($classes, $container);

        if ($container->hasParameter('sylius.config.classes')) {
            $classes = array_merge($classes, $container->getParameter('sylius.config.classes'));
        }

        $container->setParameter('sylius.config.classes', $classes);
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
                array_key_exists('templates', $config) ? $config['templates'] : null
            )->load($config['classes']);
        }
    }
}
