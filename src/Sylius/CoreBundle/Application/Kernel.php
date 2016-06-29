<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\Application;

use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class Kernel extends HttpKernel
{
    const VERSION = '0.19.0-dev';
    const VERSION_ID = '00190';
    const MAJOR_VERSION = '0';
    const MINOR_VERSION = '19';
    const RELEASE_VERSION = '0';
    const EXTRA_VERSION = 'DEV';

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            new \Sylius\InstallerBundle\SyliusInstallerBundle(),
            new \Sylius\OrderBundle\SyliusOrderBundle(),
            new \Sylius\MoneyBundle\SyliusMoneyBundle(),
            new \Sylius\CurrencyBundle\SyliusCurrencyBundle(),
            new \Sylius\ContactBundle\SyliusContactBundle(),
            new \Sylius\LocaleBundle\SyliusLocaleBundle(),
            new \Sylius\SettingsBundle\SyliusSettingsBundle(),
            new \Sylius\CartBundle\SyliusCartBundle(),
            new \Sylius\ProductBundle\SyliusProductBundle(),
            new \Sylius\ArchetypeBundle\SyliusArchetypeBundle(),
            new \Sylius\ChannelBundle\SyliusChannelBundle(),
            new \Sylius\VariationBundle\SyliusVariationBundle(),
            new \Sylius\AttributeBundle\SyliusAttributeBundle(),
            new \Sylius\TaxationBundle\SyliusTaxationBundle(),
            new \Sylius\ShippingBundle\SyliusShippingBundle(),
            new \Sylius\PaymentBundle\SyliusPaymentBundle(),
            new \Sylius\MailerBundle\SyliusMailerBundle(),
            new \Sylius\ReportBundle\SyliusReportBundle(),
            new \Sylius\PromotionBundle\SyliusPromotionBundle(),
            new \Sylius\AddressingBundle\SyliusAddressingBundle(),
            new \Sylius\InventoryBundle\SyliusInventoryBundle(),
            new \Sylius\TaxonomyBundle\SyliusTaxonomyBundle(),
            new \Sylius\FlowBundle\SyliusFlowBundle(),
            new \Sylius\PricingBundle\SyliusPricingBundle(),
            new \Sylius\SequenceBundle\SyliusSequenceBundle(),
            new \Sylius\ContentBundle\SyliusContentBundle(),
            new \Sylius\SearchBundle\SyliusSearchBundle(),
            new \Sylius\RbacBundle\SyliusRbacBundle(),
            new \Sylius\UserBundle\SyliusUserBundle(),
            new \Sylius\UiBundle\SyliusUiBundle(),
            new \Sylius\AdminBundle\SyliusAdminBundle(),
            new \Sylius\ShopBundle\SyliusShopBundle(),
            new \Sylius\MetadataBundle\SyliusMetadataBundle(),
            new \Sylius\AssociationBundle\SyliusAssociationBundle(),
            new \Sylius\ReviewBundle\SyliusReviewBundle(),
            new \Sylius\CoreBundle\SyliusCoreBundle(),
            new \Sylius\WebBundle\SyliusWebBundle(),
            new \Sylius\ResourceBundle\SyliusResourceBundle(),
            new \Sylius\GridBundle\SyliusGridBundle(),
            new \winzou\Bundle\StateMachineBundle\winzouStateMachineBundle(),
            new \Sylius\ApiBundle\SyliusApiBundle(),

            new \Sonata\BlockBundle\SonataBlockBundle(),
            new \Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
            new \Symfony\Cmf\Bundle\BlockBundle\CmfBlockBundle(),
            new \Symfony\Cmf\Bundle\ContentBundle\CmfContentBundle(),
            new \Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new \Symfony\Cmf\Bundle\MenuBundle\CmfMenuBundle(),
            new \Symfony\Cmf\Bundle\CreateBundle\CmfCreateBundle(),
            new \Symfony\Cmf\Bundle\MediaBundle\CmfMediaBundle(),

            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new \Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),

            new \Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
            new \FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new \FOS\RestBundle\FOSRestBundle(),

            new \FOS\ElasticaBundle\FOSElasticaBundle(),
            new \Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new \Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new \Liip\ImagineBundle\LiipImagineBundle(),
            new \Payum\Bundle\PayumBundle\PayumBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new \JMS\TranslationBundle\JMSTranslationBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),

            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new \Sylius\FixturesBundle\SyliusFixturesBundle(),
            new \Sylius\PayumBundle\SyliusPayumBundle(), // must be added after PayumBundle.
            new \Sylius\ThemeBundle\SyliusThemeBundle(), // must be added after FrameworkBundle
        ];

        if (in_array($this->environment, ['dev', 'test', 'test_cached'], true)) {
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerBaseClass()
    {
        if ('test' === $this->environment || 'test_cached' === $this->environment) {
            return MockerContainer::class;
        }

        return parent::getContainerBaseClass();
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $rootDir = $this->getRootDir();

        $loader->load($rootDir.'/config/config_'.$this->environment.'.yml');

        if (is_file($file = $rootDir.'/config/config_'.$this->environment.'.local.yml')) {
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
     * @return bool
     */
    protected function isVagrantEnvironment()
    {
        return (getenv('HOME') === '/home/vagrant' || getenv('VAGRANT') === 'VAGRANT') && is_dir('/dev/shm');
    }
}
