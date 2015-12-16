<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Core extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusCoreExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * @var array
     */
    private $bundles = array(
        'sylius_addressing',
        'sylius_api',
        'sylius_attribute',
        'sylius_channel',
        'sylius_contact',
        'sylius_currency',
        'sylius_inventory',
        'sylius_locale',
        'sylius_order',
        'sylius_payment',
        'sylius_payum',
        'sylius_product',
        'sylius_promotion',
        'sylius_report',
        'sylius_search',
        'sylius_sequence',
        'sylius_settings',
        'sylius_shipping',
        'sylius_mailer',
        'sylius_taxation',
        'sylius_taxonomy',
        'sylius_user',
        'sylius_variation',
        'sylius_translation',
        'sylius_rbac',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $configFiles = array(
            'services.xml',
            'controller.xml',
            'form.xml',
            'api_form.xml',
            'templating.xml',
            'twig.xml',
            'reports.xml',
            'state_machine.xml',
            'email.xml',
        );

        $env = $container->getParameter('kernel.environment');
        if ('test' === $env || 'test_cached' === $env) {
            $configFiles[] = 'test_services.xml';
        }

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $this->loadCheckoutConfiguration($config['checkout'], $container);

        $definition = $container->findDefinition('sylius.context.currency');
        $definition->replaceArgument(0, new Reference($config['currency_storage']));
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        foreach ($container->getExtensions() as $name => $extension) {
            if (in_array($name, $this->bundles)) {
                $container->prependExtensionConfig($name, array('driver' => $config['driver']));
            }
        }

        $routeClasses = $controllerByClasses = $repositoryByClasses = $syliusByClasses = array();

        foreach ($config['routing'] as $className => $routeConfig) {
            $routeClasses[$className] = array(
                'field'  => $routeConfig['field'],
                'prefix' => $routeConfig['prefix'],
            );
            $controllerByClasses[$className] = $routeConfig['defaults']['controller'];
            $repositoryByClasses[$className] = $routeConfig['defaults']['repository'];
            $syliusByClasses[$className] = $routeConfig['defaults']['sylius'];
        }

        $container->setParameter('sylius.route_classes', $routeClasses);
        $container->setParameter('sylius.controller_by_classes', $controllerByClasses);
        $container->setParameter('sylius.repository_by_classes', $repositoryByClasses);
        $container->setParameter('sylius.sylius_by_classes', $syliusByClasses);
        $container->setParameter('sylius.route_collection_limit', $config['route_collection_limit']);
        $container->setParameter('sylius.route_uri_filter_regexp', $config['route_uri_filter_regexp']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function loadCheckoutConfiguration(array $config, ContainerBuilder $container)
    {
        foreach ($config['steps'] as $name => $step) {
            $container->setParameter(sprintf('sylius.checkout.step.%s.template', $name), $step['template']);
        }
    }
}
