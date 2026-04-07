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
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;
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

    $services
        ->set('sylius.telemetry.query.timeout_runner', TimeoutRunner::class)
        ->args([
            '%sylius_core.telemetry.query_timeout%',
        ])
    ;

    $services
        ->set('sylius.telemetry.installation_id_generator', InstallationIdGenerator::class)
        ->args([
            '%sylius_core.telemetry.salt%',
        ])
        ->lazy(InstallationIdGeneratorInterface::class)
    ;

    $services
        ->set('sylius.telemetry.data_provider.version', VersionDataProvider::class)
        ->lazy(DataProviderInterface::class)
        ->tag('sylius.telemetry.technical_data_provider')
    ;

    $services
        ->set('sylius.telemetry.data_provider.database_platform', DatabasePlatformDataProvider::class)
        ->args([
            service('doctrine'),
        ])
        ->lazy(DataProviderInterface::class)
        ->tag('sylius.telemetry.technical_data_provider')
    ;

    $services
        ->set('sylius.telemetry.data_provider.environment', EnvironmentDataProvider::class)
        ->args([
            '%kernel.environment%',
        ])
        ->lazy(DataProviderInterface::class)
        ->tag('sylius.telemetry.technical_data_provider')
    ;

    $services
        ->set('sylius.telemetry.collector.technical', TechnicalDataCollector::class)
        ->args([
            tagged_iterator('sylius.telemetry.technical_data_provider'),
            '%sylius_core.telemetry.technical%',
        ])
        ->lazy(TelemetryDataCollectorInterface::class)
        ->tag('sylius.telemetry.collector')
    ;

    $services
        ->set('sylius.telemetry.data_provider.installed_plugins', InstalledPluginsDataProvider::class)
        ->args([
            '%kernel.project_dir%',
        ])
        ->lazy(DataProviderInterface::class)
        ->tag('sylius.telemetry.plugins_data_provider')
    ;

    $services
        ->set('sylius.telemetry.collector.plugins', PluginsDataCollector::class)
        ->args([
            tagged_iterator('sylius.telemetry.plugins_data_provider'),
            '%sylius_core.telemetry.plugins%',
        ])
        ->lazy(TelemetryDataCollectorInterface::class)
        ->tag('sylius.telemetry.collector')
    ;

    $services
        ->set('sylius.telemetry.data_provider.locales', LocalesDataProvider::class)
        ->args([
            service('doctrine.dbal.default_connection'),
            service('sylius.telemetry.query.timeout_runner'),
            '%locale%',
        ])
        ->lazy(DataProviderInterface::class)
        ->tag('sylius.telemetry.business_data_provider')
    ;

    $services
        ->set('sylius.telemetry.data_provider.currencies', CurrenciesDataProvider::class)
        ->args([
            service('doctrine.dbal.default_connection'),
            service('sylius.telemetry.query.timeout_runner'),
        ])
        ->lazy(DataProviderInterface::class)
        ->tag('sylius.telemetry.business_data_provider')
    ;

    $services
        ->set('sylius.telemetry.data_provider.payment_methods', PaymentMethodsDataProvider::class)
        ->args([
            service('doctrine.dbal.default_connection'),
            service('sylius.telemetry.query.timeout_runner'),
        ])
        ->lazy(DataProviderInterface::class)
        ->tag('sylius.telemetry.business_data_provider')
    ;

    $services
        ->set('sylius.telemetry.data_provider.shipping_methods', ShippingMethodsDataProvider::class)
        ->args([
            service('doctrine.dbal.default_connection'),
            service('sylius.telemetry.query.timeout_runner'),
        ])
        ->lazy(DataProviderInterface::class)
        ->tag('sylius.telemetry.business_data_provider')
    ;

    $services
        ->set('sylius.telemetry.data_provider.metrics_counts', MetricsCountsDataProvider::class)
        ->args([
            service('doctrine.dbal.default_connection'),
            service('sylius.telemetry.query.timeout_runner'),
        ])
        ->lazy(DataProviderInterface::class)
        ->tag('sylius.telemetry.business_data_provider')
    ;

    $services
        ->set('sylius.telemetry.data_provider.orders_business', OrdersBusinessDataProvider::class)
        ->args([
            service('doctrine.dbal.default_connection'),
            service('sylius.telemetry.query.timeout_runner'),
        ])
        ->lazy(DataProviderInterface::class)
        ->tag('sylius.telemetry.business_data_provider')
    ;

    $services
        ->set('sylius.telemetry.data_provider.countries', CountriesDataProvider::class)
        ->args([
            service('doctrine.dbal.default_connection'),
            service('sylius.telemetry.query.timeout_runner'),
        ])
        ->lazy(DataProviderInterface::class)
        ->tag('sylius.telemetry.business_data_provider')
    ;

    $services
        ->set('sylius.telemetry.collector.business', BusinessDataCollector::class)
        ->args([
            tagged_iterator('sylius.telemetry.business_data_provider'),
            '%sylius_core.telemetry.business%',
        ])
        ->lazy(TelemetryDataCollectorInterface::class)
        ->tag('sylius.telemetry.collector')
    ;

    $services
        ->set('sylius.telemetry.orchestrator', TelemetryOrchestrator::class)
        ->args([
            service('sylius.telemetry.installation_id_generator'),
            tagged_iterator('sylius.telemetry.collector'),
        ])
        ->lazy(TelemetryOrchestratorInterface::class)
    ;

    $services
        ->set('sylius.telemetry.cache', TelemetryCache::class)
        ->args([
            service('cache.app'),
        ])
        ->lazy(TelemetryCacheInterface::class)
    ;

    $services
        ->set('sylius.telemetry.send_manager', TelemetrySendManager::class)
        ->args([
            service('sylius.telemetry.orchestrator'),
            service('sylius.telemetry.cache'),
            service('sylius.telemetry.sender'),
        ])
        ->lazy(TelemetrySendManagerInterface::class)
    ;

    $services
        ->set('sylius.telemetry.listener', TelemetryListener::class)
        ->args([
            service('sylius.telemetry.send_manager'),
            service('sylius.telemetry.cache'),
            '%sylius.security.api_admin_route%',
        ])
        ->lazy()
        ->tag('kernel.event_listener', [
            'event' => 'kernel.terminate',
            'method' => 'onAdminAccess',
            'priority' => -1024,
        ])
    ;

    $services
        ->set('sylius.telemetry.sender', TelemetrySender::class)
        ->args([
            inline_service(HttpClient::class)
                ->factory([HttpClient::class, 'create']),
            '%sylius_core.telemetry.url%',
        ])
        ->lazy(TelemetrySenderInterface::class)
    ;

    $services
        ->set('sylius.telemetry.listener.notice', TelemetryNoticeListener::class)
        ->args([
            service('cache.app'),
        ])
        ->tag('kernel.event_listener', [
            'event' => 'console.terminate',
            'method' => 'onConsoleTerminate',
        ])
    ;
};
