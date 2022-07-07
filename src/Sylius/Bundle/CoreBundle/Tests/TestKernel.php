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

namespace Sylius\Bundle\CoreBundle\Tests;

use BabDev\PagerfantaBundle\BabDevPagerfantaBundle;
use Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use FOS\RestBundle\FOSRestBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Knp\Bundle\GaufretteBundle\KnpGaufretteBundle;
use League\FlysystemBundle\FlysystemBundle;
use Liip\ImagineBundle\LiipImagineBundle;
use Payum\Bundle\PayumBundle\PayumBundle;
use Sonata\BlockBundle\SonataBlockBundle;
use Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle;
use Sylius\Bundle\AddressingBundle\SyliusAddressingBundle;
use Sylius\Bundle\AttributeBundle\SyliusAttributeBundle;
use Sylius\Bundle\ChannelBundle\SyliusChannelBundle;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Sylius\Bundle\CurrencyBundle\SyliusCurrencyBundle;
use Sylius\Bundle\CustomerBundle\SyliusCustomerBundle;
use Sylius\Bundle\FixturesBundle\SyliusFixturesBundle;
use Sylius\Bundle\GridBundle\SyliusGridBundle;
use Sylius\Bundle\InventoryBundle\SyliusInventoryBundle;
use Sylius\Bundle\LocaleBundle\SyliusLocaleBundle;
use Sylius\Bundle\MailerBundle\SyliusMailerBundle;
use Sylius\Bundle\MoneyBundle\SyliusMoneyBundle;
use Sylius\Bundle\OrderBundle\SyliusOrderBundle;
use Sylius\Bundle\PaymentBundle\SyliusPaymentBundle;
use Sylius\Bundle\PayumBundle\SyliusPayumBundle;
use Sylius\Bundle\ProductBundle\SyliusProductBundle;
use Sylius\Bundle\PromotionBundle\SyliusPromotionBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ReviewBundle\SyliusReviewBundle;
use Sylius\Bundle\ShippingBundle\SyliusShippingBundle;
use Sylius\Bundle\TaxationBundle\SyliusTaxationBundle;
use Sylius\Bundle\TaxonomyBundle\SyliusTaxonomyBundle;
use Sylius\Bundle\ThemeBundle\SyliusThemeBundle;
use Sylius\Bundle\UiBundle\SyliusUiBundle;
use Sylius\Bundle\UserBundle\SyliusUserBundle;
use Sylius\Calendar\SyliusCalendarBundle;
use SyliusLabs\DoctrineMigrationsExtraBundle\SyliusLabsDoctrineMigrationsExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\RouteCollectionBuilder;
use winzou\Bundle\StateMachineBundle\winzouStateMachineBundle;

final class TestKernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new DoctrineBundle(),
            new SyliusCalendarBundle(),
            new SyliusOrderBundle(),
            new SyliusMoneyBundle(),
            new SyliusCurrencyBundle(),
            new SyliusLocaleBundle(),
            new SyliusProductBundle(),
            new SyliusChannelBundle(),
            new SyliusAttributeBundle(),
            new SyliusTaxationBundle(),
            new SyliusShippingBundle(),
            new SyliusPaymentBundle(),
            new SyliusMailerBundle(),
            new SyliusPromotionBundle(),
            new SyliusAddressingBundle(),
            new SyliusInventoryBundle(),
            new SyliusTaxonomyBundle(),
            new SyliusUserBundle(),
            new SyliusCustomerBundle(),
            new SyliusUiBundle(),
            new SyliusReviewBundle(),
            new SyliusCoreBundle(),
            new SyliusResourceBundle(),
            new SyliusGridBundle(),
            new winzouStateMachineBundle(),
            new BazingaHateoasBundle(),
            new JMSSerializerBundle(),
            new FOSRestBundle(),
            new KnpGaufretteBundle(),
            new FlysystemBundle(),
            new LiipImagineBundle(),
            new PayumBundle(),
            new StofDoctrineExtensionsBundle(),
            new BabDevPagerfantaBundle(),
            new SyliusFixturesBundle(),
            new SyliusPayumBundle(),
            new SyliusThemeBundle(),
            new SonataBlockBundle(),
            new DoctrineMigrationsBundle(),
            new SyliusLabsDoctrineMigrationsExtraBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $containerBuilder->setParameter('locale', 'en_US');

        $containerBuilder->loadFromExtension('framework', [
            'test' => null,
            'secret' => 'S0ME_SECRET',
            'session' => [
                'handler_id' => null,
            ],
            'default_locale' => '%locale%',
            'translator' => [
                'fallbacks' => [
                    '%locale%',
                    'en',
                ],
            ],
        ]);

        $containerBuilder->loadFromExtension('security', [
            'firewalls' => [
                'main' => [],
            ],
        ]);

        $containerBuilder->loadFromExtension('doctrine', [
            'dbal' => [
                'driver' => 'pdo_mysql',
                'server_version' => '5.7',
                'charset' => 'UTF8',
                'url' => 'sqlite:///%kernel.project_dir%/var/data.db',
            ],
        ]);

        $containerBuilder->loadFromExtension('stof_doctrine_extensions', [
            'default_locale' => '%locale%',
        ]);

        $containerBuilder->loadFromExtension('twig', [
            'debug' => '%kernel.debug%',
            'strict_variables' => '%kernel.debug%',
        ]);

        $loader->load('@SyliusCoreBundle/Resources/config/app/config.yml');
    }

    /**
     * @param RoutingConfigurator|RouteCollectionBuilder $routes
     */
    protected function configureRoutes(object $routes): void
    {
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/SyliusCoreBundle/cache/' . $this->getEnvironment();
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/SyliusCoreBundle/logs';
    }
}
