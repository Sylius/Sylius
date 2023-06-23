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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface;
use Sylius\Component\Core\Model\CatalogPromotion;
use Sylius\Component\Core\Model\CatalogPromotionInterface;

final class CatalogPromotionRemovalAnnouncerTest extends AbstractWebTestCase
{
    /** @test */
    public function it_puts_catalog_promotion_into_processing_state(): void
    {
        $this->createClient();

        $catalogPromotion = $this->getCatalogPromotion();

        /** @var CatalogPromotionRemovalAnnouncerInterface $catalogPromotionRemovalAnnouncer */
        $catalogPromotionRemovalAnnouncer = self::$kernel->getContainer()->get('Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface');
        $catalogPromotionRemovalAnnouncer->dispatchCatalogPromotionRemoval($catalogPromotion);

        $this->assertSame('processing', $catalogPromotion->getState());
    }

    /** @test */
    public function it_removes_active_catalog_promotion_when_processing_has_been_finished(): void
    {
        $this->createClient();

        $catalogPromotion = $this->getCatalogPromotion();

        /** @var CatalogPromotionRemovalAnnouncerInterface $catalogPromotionRemovalAnnouncer */
        $catalogPromotionRemovalAnnouncer = self::$kernel->getContainer()->get('Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface');
        $catalogPromotionRemovalAnnouncer->dispatchCatalogPromotionRemoval($catalogPromotion);

        $this->assertNull(self::$kernel->getContainer()->get('sylius.repository.catalog_promotion')->findOneBy(['code' => $catalogPromotion->getCode()]));
    }

    private function getCatalogPromotion(): CatalogPromotionInterface
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::$kernel->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtures = $fixtureLoader->load([__DIR__ . '/../DataFixtures/ORM/resources/catalog_promotions.yml'], [], [], PurgeMode::createDeleteMode());

        /** @var CatalogPromotion $catalogPromotion */
        $catalogPromotion = $fixtures['sale_2'];

        return $catalogPromotion;
    }
}
