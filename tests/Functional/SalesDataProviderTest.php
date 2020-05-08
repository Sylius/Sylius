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

namespace Sylius\Tests\Functional;

use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Dashboard\SalesDataProviderInterface;
use Sylius\Component\Core\Dashboard\SalesSummary;
use Sylius\Component\Core\Dashboard\SalesSummaryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;

final class SalesDataProviderTest extends WebTestCase
{
    /** @var Client */
    private static $client;

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

    /** @test */
    public function it_provides_simple_year_sales_summary_grouped_per_month(): void
    {
        $salesSummary = $this->getSummaryForChannel('CHANNEL');
        $expectedMonths = $this->getExpectedMonths();

        $this->assertInstanceOf(SalesSummary::class, $salesSummary);
        $this->assertSame($expectedMonths, $salesSummary->getMonths());
        $this->assertSame(
            ['0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '30.00', '20.00', '20.00', '0.00'],
            $salesSummary->getSales()
        );
    }

    /** @test */
    public function it_provides_different_data_for_each_channel_and_only_paid_orders(): void
    {
        $salesSummary = $this->getSummaryForChannel('EXPENSIVE_CHANNEL');
        $expectedMonths = $this->getExpectedMonths();

        $this->assertInstanceOf(SalesSummary::class, $salesSummary);
        $this->assertSame($expectedMonths, $salesSummary->getMonths());
        $this->assertSame(
            ['0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '330.00', '0.00'],
            $salesSummary->getSales()
        );
    }

    private function getSummaryForChannel(string $channelCode): SalesSummaryInterface
    {
        /** @var ChannelRepositoryInterface $channelRepository */
        $channelRepository = self::$container->get('sylius.repository.channel');
        /** @var ChannelInterface $channel */
        $channel = $channelRepository->findOneByCode($channelCode);

        /** @var SalesDataProviderInterface $salesDataProvider */
        $salesDataProvider = self::$container->get(SalesDataProviderInterface::class);

        return $salesDataProvider->getLastYearSalesSummary($channel);
    }

    private function getExpectedMonths(): array
    {
        $expectedMonths = [];
        $period = new \DatePeriod(
            new \DateTime('first day of next month last year'),
            \DateInterval::createFromDateString('1 month'),
            new \DateTime('last day of this month')
        );

        /** @var \DateTimeInterface $date */
        foreach ($period as $date) {
            $expectedMonths[] = $date->format('m.y');
        }

        return $expectedMonths;
    }
}
