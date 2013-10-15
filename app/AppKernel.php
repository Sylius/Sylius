<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\ClassLoader\DebugUniversalClassLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Sylius kernel.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            // Sylius bundles.
            new Sylius\Bundle\InstallerBundle\SyliusInstallerBundle(),
            new Sylius\Bundle\OrderBundle\SyliusOrderBundle(),
            new Sylius\Bundle\MoneyBundle\SyliusMoneyBundle(),
            new Sylius\Bundle\SettingsBundle\SyliusSettingsBundle(),
            new Sylius\Bundle\CartBundle\SyliusCartBundle(),
            new Sylius\Bundle\ProductBundle\SyliusProductBundle(),
            new Sylius\Bundle\VariableProductBundle\SyliusVariableProductBundle(),
            new Sylius\Bundle\TaxationBundle\SyliusTaxationBundle(),
            new Sylius\Bundle\ShippingBundle\SyliusShippingBundle(),
            new Sylius\Bundle\PaymentsBundle\SyliusPaymentsBundle(),
            new Sylius\Bundle\PayumBundle\SyliusPayumBundle(),
            new Sylius\Bundle\PromotionsBundle\SyliusPromotionsBundle(),
            new Sylius\Bundle\AddressingBundle\SyliusAddressingBundle(),
            new Sylius\Bundle\InventoryBundle\SyliusInventoryBundle(),
            new Sylius\Bundle\TaxonomiesBundle\SyliusTaxonomiesBundle(),
            new Sylius\Bundle\FlowBundle\SyliusFlowBundle(),

            new Sylius\Bundle\CoreBundle\SyliusCoreBundle(),
            new Sylius\Bundle\WebBundle\SyliusWebBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),

            // CMF bundles.
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
            new Symfony\Cmf\Bundle\BlockBundle\CmfBlockBundle(),
            new Symfony\Cmf\Bundle\ContentBundle\CmfContentBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Symfony\Cmf\Bundle\MenuBundle\CmfMenuBundle(),

            // Core bundles.
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),

            // Third party bundles.
            new Liip\DoctrineCacheBundle\LiipDoctrineCacheBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new JMS\TranslationBundle\JMSTranslationBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new Payum\Bundle\PayumBundle\PayumBundle(),
        );

        if ('dev' === $this->environment) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        $bundles = $this->addFixturesBundle($bundles);

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->debug) {
            ini_set('display_errors', 1);
            error_reporting(-1);

            DebugUniversalClassLoader::enable();
            ErrorHandler::register();
            if ('cli' !== php_sapi_name()) {
                ExceptionHandler::register();
            }
        } else {
            ini_set('display_errors', 0);
        }

        ini_set('date.timezone', 'UTC');
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->environment.'.yml');

        if (is_file($file = __DIR__.'/config/config_'.$this->environment.'.local.yml')) {
            $loader->load($file);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        if ($this->isVagrantEnvironment()) {
            return '/dev/shm/sylius/cache/'.$this->environment;
        }

        return parent::getCacheDir();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        if ($this->isVagrantEnvironment()) {
            return '/dev/shm/sylius/logs';
        }

        return parent::getLogDir();
    }

    /**
     * @return boolean
     */
    private function isVagrantEnvironment()
    {
        return (getenv('HOME') === '/home/vagrant' || getenv('VAGRANT') === 'VAGRANT') && is_dir('/dev/shm');
    }

    /**
     * @param array $bundles
     * @param array $environments
     *
     * @return array
     */
    private function addFixturesBundle(array $bundles, array $environments = array('dev', 'test'))
    {
        if (in_array($this->environment, $environments) && class_exists('Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle')) {
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }
}
