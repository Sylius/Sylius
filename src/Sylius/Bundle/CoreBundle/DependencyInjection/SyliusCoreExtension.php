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

namespace Sylius\Bundle\CoreBundle\DependencyInjection;

use Sylius\Bundle\CoreBundle\Routing\Matcher\Dumper\PhpMatcherDumper;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Generator\Dumper\PhpGeneratorDumper;
use Symfony\Component\Routing\Generator\UrlGenerator;

final class SyliusCoreExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /** @var array */
    private static $bundles = [
        'sylius_addressing',
        'sylius_attribute',
        'sylius_channel',
        'sylius_currency',
        'sylius_customer',
        'sylius_inventory',
        'sylius_locale',
        'sylius_order',
        'sylius_payment',
        'sylius_payum',
        'sylius_product',
        'sylius_promotion',
        'sylius_review',
        'sylius_shipping',
        'sylius_taxation',
        'sylius_taxonomy',
        'sylius_user',
        'sylius_variation',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $env = $container->getParameter('kernel.environment');
        if ('test' === $env || 'test_cached' === $env) {
            $loader->load('test_services.xml');
        }

        // This service is temporarily overwritten, due to problems with PhpMatcherDumper in Symfony 4.1.8 and 4.1.9
        if (Kernel::VERSION === '4.1.8' || Kernel::VERSION === '4.1.9') {
            $this->overrideRouterDefinition($container);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $config = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);

        foreach ($container->getExtensions() as $name => $extension) {
            if (in_array($name, self::$bundles, true)) {
                $container->prependExtensionConfig($name, ['driver' => $config['driver']]);
            }
        }

        $container->prependExtensionConfig('sylius_theme', ['context' => 'sylius.theme.context.channel_based']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $this->prependHwiOauth($container, $loader);
    }

    private function prependHwiOauth(ContainerBuilder $container, LoaderInterface $loader): void
    {
        if (!$container->hasExtension('hwi_oauth')) {
            return;
        }

        $loader->load('services/integrations/hwi_oauth.xml');
    }

    private function overrideRouterDefinition(ContainerBuilder $container): void
    {
        $routerDefinition = new Definition(Router::class);
        $routerDefinition->addTag('monolog.logger', ['channel' => 'router']);
        $routerDefinition->addTag('container.service_subscriber', ['id' => 'routing.loader']);
        $routerDefinition->addArgument(new Reference('Psr\Container\ContainerInterface'));
        $routerDefinition->addArgument($container->getParameter('router.resource'));
        $routerDefinition->addArgument([
            'cache_dir' => $container->getParameter('kernel.cache_dir'),
            'debug' => $container->getParameter('kernel.debug'),
            'generator_class' => UrlGenerator::class,
            'generator_base_class' => UrlGenerator::class,
            'generator_dumper_class' => PhpGeneratorDumper::class,
            'generator_cache_class' => $container->getParameter('router.cache_class_prefix') . 'UrlGenerator',
            'matcher_class' => RedirectableUrlMatcher::class,
            'matcher_base_class' => RedirectableUrlMatcher::class,
            'matcher_dumper_class' => PhpMatcherDumper::class,
            'matcher_cache_class' => $container->getParameter('router.cache_class_prefix') . 'UrlMatcher',
        ]);
        $routerDefinition->addArgument((new Reference('router.request_context', \Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE)));
        $routerDefinition->addArgument((new Reference('parameter_bag', \Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE)));
        $routerDefinition->addArgument((new Reference('logger', \Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE)));
        $routerDefinition->addMethodCall('setConfigCacheFactory', [new Reference('config_cache_factory')]);

        $container->setDefinition('router.default', $routerDefinition);
    }
}
