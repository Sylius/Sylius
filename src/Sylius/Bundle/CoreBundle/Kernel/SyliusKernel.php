<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Sylius kernel.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class SyliusKernel extends Kernel
{
    const VERSION = '0.9.0';

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            new \Sylius\Bundle\InstallerBundle\SyliusInstallerBundle(),
            new \Sylius\Bundle\OrderBundle\SyliusOrderBundle(),
            new \Sylius\Bundle\MoneyBundle\SyliusMoneyBundle(),
            new \Sylius\Bundle\SettingsBundle\SyliusSettingsBundle(),
            new \Sylius\Bundle\CartBundle\SyliusCartBundle(),
            new \Sylius\Bundle\ProductBundle\SyliusProductBundle(),
            new \Sylius\Bundle\VariationBundle\SyliusVariationBundle(),
            new \Sylius\Bundle\AttributeBundle\SyliusAttributeBundle(),
            new \Sylius\Bundle\TaxationBundle\SyliusTaxationBundle(),
            new \Sylius\Bundle\ShippingBundle\SyliusShippingBundle(),
            new \Sylius\Bundle\PaymentBundle\SyliusPaymentBundle(),
            new \Sylius\Bundle\PayumBundle\SyliusPayumBundle(),
            new \Sylius\Bundle\PromotionBundle\SyliusPromotionBundle(),
            new \Sylius\Bundle\AddressingBundle\SyliusAddressingBundle(),
            new \Sylius\Bundle\InventoryBundle\SyliusInventoryBundle(),
            new \Sylius\Bundle\TaxonomyBundle\SyliusTaxonomyBundle(),
            new \Sylius\Bundle\FlowBundle\SyliusFlowBundle(),

            new \Sylius\Bundle\CoreBundle\SyliusCoreBundle(),
            new \Sylius\Bundle\WebBundle\SyliusWebBundle(),
            new \Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),

            new \Sonata\BlockBundle\SonataBlockBundle(),
            new \Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
            new \Symfony\Cmf\Bundle\BlockBundle\CmfBlockBundle(),
            new \Symfony\Cmf\Bundle\ContentBundle\CmfContentBundle(),
            new \Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new \Symfony\Cmf\Bundle\MenuBundle\CmfMenuBundle(),

            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),

            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new \Liip\ImagineBundle\LiipImagineBundle(),
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new \Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle($this),
            new \HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new \FOS\RestBundle\FOSRestBundle(),
            new \FOS\UserBundle\FOSUserBundle(),
            new \WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \JMS\TranslationBundle\JMSTranslationBundle(),
            new \Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new \Payum\Bundle\PayumBundle\PayumBundle(),
        );

        if ('dev' === $this->environment) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        if (in_array($this->environment, array('dev', 'test')) && class_exists('Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle')) {
            $bundles[] = new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new \Sylius\Bundle\FixturesBundle\SyliusFixturesBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $appDir = $this->getAppDir();

        $loader->load($appDir.'/config/config_'.$this->environment.'.yml');

        if (is_file($file = $appDir.'/config/config_'.$this->environment.'.local.yml')) {
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
    protected function isVagrantEnvironment()
    {
        return (getenv('HOME') === '/home/vagrant' || getenv('VAGRANT') === 'VAGRANT') && is_dir('/dev/shm');
    }

    /**
     * @return string
     */
    protected function getAppDir()
    {
        $reflection = new \ReflectionClass(get_class($this));

        return dirname($reflection->getFilename());
    }
}
