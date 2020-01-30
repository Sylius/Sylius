<?php

declare(strict_types=1);

namespace Sylius\Tests\Functional;

use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Dashboard\SalesDataProviderInterface;
use Sylius\Component\Core\Dashboard\SalesSummary;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;

final class SalesDataProviderTest extends WebTestCase
{
    /** @var Client */
    private static $client;

    /** @test */
    public function it_provides_simple_year_sales_summary_grouped_per_month(): void
    {
        $salesSummary = $this->getSummaryForChannel('CHANNEL');

        $this->assertInstanceOf(SalesSummary::class, $salesSummary);
        $this->assertSame(
            [(new \DateTime('-1 month'))->format('m.y'), (new \DateTime('-2 month'))->format('m.y'), (new \DateTime('-3 month'))->format('m.y')],
            $salesSummary->getMonths()
        );
        $this->assertSame(['20.00', '20.00', '30.00'], $salesSummary->getSales());
    }

    /** @test */
    public function it_provides_different_data_for_each_channel(): void
    {
        $salesSummary = $this->getSummaryForChannel('EXPENSIVE_CHANNEL');

        $this->assertInstanceOf(SalesSummary::class, $salesSummary);
        $this->assertSame(
            [(new \DateTime('-1 month'))->format('m.y')],
            $salesSummary->getMonths()
        );
        $this->assertSame(['330.00'], $salesSummary->getSales());
    }

    protected function setUp(): void
    {
        self::$client = static::createClient();
        self::$client->followRedirects(true);
        self::$container = self::$client->getContainer();

        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::$container->get('fidry_alice_data_fixtures.loader.doctrine');

        $fixtureLoader->load([__DIR__ . '/../DataFixtures/ORM/resources/year_sales.yml'], [], [], PurgeMode::createDeleteMode());

        /** @var OrderInterface[] $orders */
        $orders = self::$container->get('sylius.repository.order')->findAll();
        /** @var OrderProcessorInterface $orderProcessor */
        $orderProcessor = self::$container->get('sylius.order_processing.order_processor');

        foreach ($orders as $order) {
            $orderProcessor->process($order);
        }

        self::$container->get('sylius.manager.order')->flush();
    }

    private function getSummaryForChannel(string $channelCode): SalesSummary
    {
        /** @var ChannelRepositoryInterface $channelRepository */
        $channelRepository = self::$container->get('sylius.repository.channel');
        /** @var ChannelInterface $channel */
        $channel = $channelRepository->findOneByCode($channelCode);

        /** @var SalesDataProviderInterface $salesDataProvider */
        $salesDataProvider = self::$container->get('Sylius\Component\Core\Dashboard\SalesDataProviderInterface');

        return $salesDataProvider->getLastYearSalesSummary($channel);
    }
}
