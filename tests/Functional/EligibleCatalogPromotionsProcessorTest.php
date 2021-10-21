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
        self::$container = self::$client->getContainer();

        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::$container->get('fidry_alice_data_fixtures.loader.doctrine');

        $fixtureLoader->load([__DIR__ . '/../DataFixtures/ORM/resources/catalog_promotions.yml'], [], [], PurgeMode::createDeleteMode());
    }

    /** @test */
    public function it_provides_catalog_promotions_with_precision_to_seconds(): void
    {
        /** @var EligibleCatalogPromotionsProvider $eligibleCatalogPromotionsProvider */
        $eligibleCatalogPromotionsProvider = self::$container->get('Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface');

        file_put_contents(self::$kernel->getProjectDir() . '/var/temporaryDate.txt', '2021-10-12 00:00:02');

        $dateRangeCriteria = self::$container->get('sylius.catalog_promotion.criteria.date_range');

        $eligibleCatalogPromotions = $eligibleCatalogPromotionsProvider->provide([$dateRangeCriteria]);

        $expectedDateTimes = [
            new \DateTime('2021-10-12 00:00:00'),
            new \DateTime('2021-10-12 00:00:01'),
            new \DateTime('2021-10-12 00:00:02'),
        ];

        $actualDateTimes = [];

        /** @var CatalogPromotionInterface $eligibleCatalogPromotion */
        foreach ($eligibleCatalogPromotions as $eligibleCatalogPromotion) {
            $actualDateTimes[] = $eligibleCatalogPromotion->getStartDate();
        }

        $this->assertTrue(($expectedDateTimes == $actualDateTimes));

        unlink(self::$kernel->getProjectDir() . '/var/temporaryDate.txt');
    }
}
