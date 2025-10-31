<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\CoreBundle\EventListener\TelemetryNoticeListener;
use Sylius\Bundle\CoreBundle\Telemetry\Collector\BusinessDataCollector;
use Sylius\Bundle\CoreBundle\Telemetry\Collector\PluginsDataCollector;
use Sylius\Bundle\CoreBundle\Telemetry\Collector\TechnicalDataCollector;
use Sylius\Bundle\CoreBundle\Telemetry\EventListener\TelemetryListener;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\CountriesDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\CurrenciesDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\LocalesDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\MetricsCountsDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\OrdersBusinessDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\PaymentMethodsDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\ShippingMethodsDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Plugins\InstalledPluginsDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Technical\DatabasePlatformDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Technical\EnvironmentDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Technical\VersionDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Sender\TelemetrySender;
use Sylius\Component\Core\Telemetry\Cache\TelemetryCache;
use Sylius\Component\Core\Telemetry\Cache\TelemetryCacheInterface;
use Sylius\Component\Core\Telemetry\Collector\TelemetryDataCollectorInterface;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\Generator\InstallationIdGenerator;
use Sylius\Component\Core\Telemetry\Generator\InstallationIdGeneratorInterface;
use Sylius\Component\Core\Telemetry\Sender\TelemetrySenderInterface;
use Sylius\Component\Core\Telemetry\TelemetryOrchestrator;
use Sylius\Component\Core\Telemetry\TelemetryOrchestratorInterface;
use Sylius\Component\Core\Telemetry\TelemetrySendManager;
use Sylius\Component\Core\Telemetry\TelemetrySendManagerInterface;
use Symfony\Component\HttpClient\HttpClient;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius.telemetry.installation_id_generator', InstallationIdGenerator::class)
        ->lazy(InstallationIdGeneratorInterface::class)
        ->args([
            param('sylius_core.telemetry.salt'),
        ]);

    $services->set('sylius.telemetry.data_provider.version', VersionDataProvider::class)
        ->lazy(DataProviderInterface::class)
        ->tag('sylius.telemetry.technical_data_provider');

    $services->set('sylius.telemetry.data_provider.database_platform', DatabasePlatformDataProvider::class)
        ->lazy(DataProviderInterface::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('sylius.telemetry.technical_data_provider');

    $services->set('sylius.telemetry.data_provider.environment', EnvironmentDataProvider::class)
        ->lazy(DataProviderInterface::class)
        ->args([
            param('kernel.environment'),
        ])
        ->tag('sylius.telemetry.technical_data_provider');

    $services->set('sylius.telemetry.collector.technical', TechnicalDataCollector::class)
        ->lazy(TelemetryDataCollectorInterface::class)
        ->args([
            tagged_iterator('sylius.telemetry.technical_data_provider'),
            param('sylius_core.telemetry.technical'),
        ])
        ->tag('sylius.telemetry.collector');

    $services->set('sylius.telemetry.data_provider.installed_plugins', InstalledPluginsDataProvider::class)
        ->lazy(DataProviderInterface::class)
        ->args([
            param('kernel.project_dir'),
        ])
        ->tag('sylius.telemetry.plugins_data_provider');

    $services->set('sylius.telemetry.collector.plugins', PluginsDataCollector::class)
        ->lazy(TelemetryDataCollectorInterface::class)
        ->args([
            tagged_iterator('sylius.telemetry.plugins_data_provider'),
            param('sylius_core.telemetry.plugins'),
        ])
        ->tag('sylius.telemetry.collector');

    $services->set('sylius.telemetry.data_provider.locales', LocalesDataProvider::class)
        ->lazy(DataProviderInterface::class)
        ->args([
            service('doctrine.dbal.default_connection'),
            param('locale'),
        ])
        ->tag('sylius.telemetry.business_data_provider');

    $services->set('sylius.telemetry.data_provider.currencies', CurrenciesDataProvider::class)
        ->lazy(DataProviderInterface::class)
        ->args([
            service('doctrine.dbal.default_connection'),
        ])
        ->tag('sylius.telemetry.business_data_provider');

    $services->set('sylius.telemetry.data_provider.payment_methods', PaymentMethodsDataProvider::class)
        ->lazy(DataProviderInterface::class)
        ->args([
            service('doctrine.dbal.default_connection'),
        ])
        ->tag('sylius.telemetry.business_data_provider');

    $services->set('sylius.telemetry.data_provider.shipping_methods', ShippingMethodsDataProvider::class)
        ->lazy(DataProviderInterface::class)
        ->args([
            service('doctrine.dbal.default_connection'),
        ])
        ->tag('sylius.telemetry.business_data_provider');

    $services->set('sylius.telemetry.data_provider.metrics_counts', MetricsCountsDataProvider::class)
        ->lazy(DataProviderInterface::class)
        ->args([
            service('doctrine.dbal.default_connection'),
        ])
        ->tag('sylius.telemetry.business_data_provider');

    $services->set('sylius.telemetry.data_provider.orders_business', OrdersBusinessDataProvider::class)
        ->lazy(DataProviderInterface::class)
        ->args([
            service('doctrine.dbal.default_connection'),
        ])
        ->tag('sylius.telemetry.business_data_provider');

    $services->set('sylius.telemetry.data_provider.countries', CountriesDataProvider::class)
        ->lazy(DataProviderInterface::class)
        ->args([
            service('doctrine.dbal.default_connection'),
        ])
        ->tag('sylius.telemetry.business_data_provider');

    $services->set('sylius.telemetry.collector.business', BusinessDataCollector::class)
        ->lazy(TelemetryDataCollectorInterface::class)
        ->args([
            tagged_iterator('sylius.telemetry.business_data_provider'),
            param('sylius_core.telemetry.business'),
        ])
        ->tag('sylius.telemetry.collector');

    $services->set('sylius.telemetry.orchestrator', TelemetryOrchestrator::class)
        ->lazy(TelemetryOrchestratorInterface::class)
        ->args([
            service('sylius.telemetry.installation_id_generator'),
            tagged_iterator('sylius.telemetry.collector'),
        ]);

    $services->set('sylius.telemetry.cache', TelemetryCache::class)
        ->lazy(TelemetryCacheInterface::class)
        ->args([
            service('cache.app'),
        ]);

    $services->set('sylius.telemetry.send_manager', TelemetrySendManager::class)
        ->lazy(TelemetrySendManagerInterface::class)
        ->args([
            service('sylius.telemetry.orchestrator'),
            service('sylius.telemetry.cache'),
            service('sylius.telemetry.sender'),
        ]);

    $services->set('sylius.telemetry.listener', TelemetryListener::class)
        ->lazy()
        ->args([
            service('sylius.telemetry.send_manager'),
            param('sylius.security.api_admin_route'),
        ])
        ->tag('kernel.event_listener', [
            'event' => 'kernel.terminate',
            'method' => 'onAdminAccess',
            'priority' => -1024,
        ]);

    $services->set('sylius.telemetry.sender', TelemetrySender::class)
        ->lazy(TelemetrySenderInterface::class)
        ->args([
            inline_service(HttpClient::class)
                ->factory([HttpClient::class, 'create']),
            param('sylius_core.telemetry.url'),
        ]);

    $services->set('sylius.telemetry.listener.notice', TelemetryNoticeListener::class)
        ->args([
            service('cache.app'),
        ])
        ->tag('kernel.event_listener', [
            'event' => 'console.terminate',
            'method' => 'onConsoleTerminate',
        ]);
};
