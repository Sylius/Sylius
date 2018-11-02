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

use ProxyManager\Proxy\VirtualProxyInterface;
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Webmozart\Assert\Assert;

/** @final */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    private const IGNORED_SERVICES_DURING_CLEANUP = [
        'kernel',
        'http_kernel',
        'liip_imagine.mime_type_guesser',
        'liip_imagine.extension_guesser',
    ];

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/log';
    }

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    public function shutdown(): void
    {
        if (!$this->isTestEnvironment()) {
            parent::shutdown();

            return;
        }

        if (false === $this->booted) {
            return;
        }

        $container = $this->getContainer();

        parent::shutdown();

        $this->cleanupContainer($container);
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir() . '/config';

        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getProjectDir() . '/config';

        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
    }

    protected function getContainerBaseClass(): string
    {
        if ($this->isTestEnvironment()) {
            return MockerContainer::class;
        }

        return parent::getContainerBaseClass();
    }

    protected function getContainerLoader(ContainerInterface $container): LoaderInterface
    {
        /** @var ContainerBuilder $container */
        Assert::isInstanceOf($container, ContainerBuilder::class);

        $locator = new FileLocator($this, $this->getRootDir() . '/Resources');
        $resolver = new LoaderResolver(array(
            new XmlFileLoader($container, $locator),
            new YamlFileLoader($container, $locator),
            new IniFileLoader($container, $locator),
            new PhpFileLoader($container, $locator),
            new GlobFileLoader($container, $locator),
            new DirectoryLoader($container, $locator),
            new ClosureLoader($container),
        ));

        return new DelegatingLoader($resolver);
    }

    private function isTestEnvironment(): bool
    {
        return 0 === strpos($this->getEnvironment(), 'test');
    }

    /**
     * Remove all container references from all loaded services
     */
    private function cleanupContainer(ContainerInterface $container): void
    {
        $containerReflection = new \ReflectionObject($container);
        $containerServicesPropertyReflection = $containerReflection->getProperty('services');
        $containerServicesPropertyReflection->setAccessible(true);

        $services = $containerServicesPropertyReflection->getValue($container) ?: [];
        foreach ($services as $serviceId => $service) {
            if (null === $service) {
                continue;
            }

            if (in_array($serviceId, self::IGNORED_SERVICES_DURING_CLEANUP, true)) {
                continue;
            }

            $serviceReflection = new \ReflectionObject($service);

            if ($serviceReflection->implementsInterface(VirtualProxyInterface::class)) {
                continue;
            }

            $servicePropertiesReflections = $serviceReflection->getProperties();
            $servicePropertiesDefaultValues = $serviceReflection->getDefaultProperties();
            foreach ($servicePropertiesReflections as $servicePropertyReflection) {
                $defaultPropertyValue = null;
                if (isset($servicePropertiesDefaultValues[$servicePropertyReflection->getName()])) {
                    $defaultPropertyValue = $servicePropertiesDefaultValues[$servicePropertyReflection->getName()];
                }

                $servicePropertyReflection->setAccessible(true);
                $servicePropertyReflection->setValue($service, $defaultPropertyValue);
            }
        }

        $containerServicesPropertyReflection->setValue($container, null);
    }
}
