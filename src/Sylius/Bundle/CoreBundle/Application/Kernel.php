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

namespace Sylius\Bundle\CoreBundle\Application;

use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;
use Webmozart\Assert\Assert;

class Kernel extends HttpKernel
{
    public const VERSION = '1.2.8-DEV';
    public const VERSION_ID = '10208';
    public const MAJOR_VERSION = '1';
    public const MINOR_VERSION = '2';
    public const RELEASE_VERSION = '8';
    public const EXTRA_VERSION = 'DEV';

    /**
     * {@inheritdoc}
     */
    public function registerBundles(): array
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),

            new \Sylius\Bundle\OrderBundle\SyliusOrderBundle(),
            new \Sylius\Bundle\MoneyBundle\SyliusMoneyBundle(),
            new \Sylius\Bundle\CurrencyBundle\SyliusCurrencyBundle(),
            new \Sylius\Bundle\LocaleBundle\SyliusLocaleBundle(),
            new \Sylius\Bundle\ProductBundle\SyliusProductBundle(),
            new \Sylius\Bundle\ChannelBundle\SyliusChannelBundle(),
            new \Sylius\Bundle\AttributeBundle\SyliusAttributeBundle(),
            new \Sylius\Bundle\TaxationBundle\SyliusTaxationBundle(),
            new \Sylius\Bundle\ShippingBundle\SyliusShippingBundle(),
            new \Sylius\Bundle\PaymentBundle\SyliusPaymentBundle(),
            new \Sylius\Bundle\MailerBundle\SyliusMailerBundle(),
            new \Sylius\Bundle\PromotionBundle\SyliusPromotionBundle(),
            new \Sylius\Bundle\AddressingBundle\SyliusAddressingBundle(),
            new \Sylius\Bundle\InventoryBundle\SyliusInventoryBundle(),
            new \Sylius\Bundle\TaxonomyBundle\SyliusTaxonomyBundle(),
            new \Sylius\Bundle\UserBundle\SyliusUserBundle(),
            new \Sylius\Bundle\CustomerBundle\SyliusCustomerBundle(),
            new \Sylius\Bundle\UiBundle\SyliusUiBundle(),
            new \Sylius\Bundle\ReviewBundle\SyliusReviewBundle(),
            new \Sylius\Bundle\CoreBundle\SyliusCoreBundle(),
            new \Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new \Sylius\Bundle\GridBundle\SyliusGridBundle(),
            new \winzou\Bundle\StateMachineBundle\winzouStateMachineBundle(),

            new \Sonata\CoreBundle\SonataCoreBundle(),
            new \Sonata\BlockBundle\SonataBlockBundle(),
            new \Sonata\IntlBundle\SonataIntlBundle(),
            new \Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new \FOS\RestBundle\FOSRestBundle(),

            new \Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new \Liip\ImagineBundle\LiipImagineBundle(),
            new \Payum\Bundle\PayumBundle\PayumBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),

            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new \Sylius\Bundle\FixturesBundle\SyliusFixturesBundle(),
            new \Sylius\Bundle\PayumBundle\SyliusPayumBundle(), // must be added after PayumBundle.
            new \Sylius\Bundle\ThemeBundle\SyliusThemeBundle(), // must be added after FrameworkBundle

            new \Symfony\Bundle\WebServerBundle\WebServerBundle(), // allows to run build in web server. Not recommended for prod environment
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test', 'test_cached'], true)) {
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerBaseClass(): string
    {
        if (in_array($this->getEnvironment(), ['test', 'test_cached'], true)) {
            return MockerContainer::class;
        }

        return parent::getContainerBaseClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerLoader(ContainerInterface $container): LoaderInterface
    {
        /** @var ContainerBuilder $container */
        Assert::isInstanceOf($container, ContainerBuilder::class);

        $locator = new FileLocator($this, $this->getRootDir() . '/Resources');
        $resolver = new LoaderResolver([
            new XmlFileLoader($container, $locator),
            new YamlFileLoader($container, $locator),
            new IniFileLoader($container, $locator),
            new PhpFileLoader($container, $locator),
            new DirectoryLoader($container, $locator),
            new ClosureLoader($container),
        ]);

        return new DelegatingLoader($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');

        $file = $this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.local.yml';
        if (is_file($file)) {
            $loader->load($file);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir(): string
    {
        if ($this->isVagrantEnvironment()) {
            return '/dev/shm/sylius/cache/' . $this->getEnvironment();
        }

        return dirname($this->getRootDir()) . '/var/cache/' . $this->getEnvironment();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir(): string
    {
        if ($this->isVagrantEnvironment()) {
            return '/dev/shm/sylius/logs';
        }

        return dirname($this->getRootDir()) . '/var/logs';
    }

    /**
     * @return bool
     */
    protected function isVagrantEnvironment(): bool
    {
        return (getenv('HOME') === '/home/vagrant' || getenv('VAGRANT') === 'VAGRANT') && is_dir('/dev/shm');
    }
}
