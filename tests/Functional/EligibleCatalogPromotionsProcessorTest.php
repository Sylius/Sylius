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

namespace Sylius\Tests\Functional;

use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Bundle\PromotionBundle\Criteria\DateRange;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProvider;
use Sylius\Component\Core\Dashboard\Interval;
use Sylius\Component\Core\Dashboard\SalesSummary;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EligibleCatalogPromotionsProcessorTest extends WebTestCase
{
    /** @var Client */
    private static $client;

    protected function setUp(): void
    {
        self::$client = static::createClient();
        self::$client->followRedirects(true);

        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::$kernel->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        $fixtureLoader->load([__DIR__ . '/../DataFixtures/ORM/resources/scheduled_catalog_promotions.yml'], [], [], PurgeMode::createDeleteMode());
    }

    /** @test */
    public function it_provides_catalog_promotions_with_precision_to_seconds(): void
    {
        /** @var EligibleCatalogPromotionsProvider $eligibleCatalogPromotionsProvider */
        $eligibleCatalogPromotionsProvider = self::$kernel->getContainer()->get('Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface');

        file_put_contents(self::$kernel->getProjectDir() . '/var/temporaryDate.txt', '2021-10-12 00:00:02');

        $eligibleCatalogPromotions = $eligibleCatalogPromotionsProvider->provide();

        $expectedDateTimes = [
            new \DateTime('2021-10-12 00:00:00'),
            new \DateTime('2021-10-12 00:00:01'),
            new \DateTime('2021-10-12 00:00:02'),
        ];

        $actualDateTimes = array_map(
            fn (CatalogPromotionInterface $eligibleCatalogPromotion) => $eligibleCatalogPromotion->getStartDate(),
            $eligibleCatalogPromotions
        );

        foreach ($actualDateTimes as $actualDateTime) {
            $this->assertTrue(in_array($actualDateTime, $expectedDateTimes));
        }

        unlink(self::$kernel->getProjectDir() . '/var/temporaryDate.txt');
    }

    /** @test */
    public function it_provides_catalog_promotions_with_precision_to_seconds_for_end_date(): void
    {
        /** @var EligibleCatalogPromotionsProvider $eligibleCatalogPromotionsProvider */
        $eligibleCatalogPromotionsProvider = self::$kernel->getContainer()->get('Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface');

        file_put_contents(self::$kernel->getProjectDir() . '/var/temporaryDate.txt', '2021-10-12 23:59:58');

        $eligibleCatalogPromotions = $eligibleCatalogPromotionsProvider->provide();

        $expectedDateTimes = [
            new \DateTime('2021-10-12 23:59:59'),
            new \DateTime('2021-10-12 23:59:59'),
        ];

        $actualDateTimes = [];

        /** @var CatalogPromotionInterface $eligibleCatalogPromotion */
        foreach ($eligibleCatalogPromotions as $eligibleCatalogPromotion) {
            $actualDateTimes[] = $eligibleCatalogPromotion->getEndDate();
        }

        $this->assertTrue(($expectedDateTimes == $actualDateTimes));

        unlink(self::$kernel->getProjectDir() . '/var/temporaryDate.txt');
    }
}
