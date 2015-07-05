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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
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

    protected $configFiles = array(
        'services.xml',
        'controller.xml',
        'form.xml',
        'api_form.xml',
        'templating.xml',
        'twig.xml',
        'reports.xml',
        'mailer.xml',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS
        );

        $this->loadServiceDefinitions($container, 'state_machine.xml');

        $this->loadCheckoutConfiguration($config['checkout'], $container);

        $definition = $container->findDefinition('sylius.context.currency');
        $definition->replaceArgument(0, new Reference($config['currency_storage']));

        $this->addClassesToCompile(array(
            'Symfony\Component\Config\Resource\FileResource',
            'Symfony\Component\HttpKernel\Config\FileLocator',
            'Symfony\Component\Config\Resource\DirectoryResource',
            'Symfony\Bundle\AsseticBundle\Config\AsseticResource',
            'Symfony\Bundle\AsseticBundle\Factory\Resource\FileResource',
            'Symfony\Bundle\FrameworkBundle\Templating\TemplateReference',
            'Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator',
            'Symfony\Bundle\FrameworkBundle\Templating\Loader\FilesystemLoader'
        ));
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
