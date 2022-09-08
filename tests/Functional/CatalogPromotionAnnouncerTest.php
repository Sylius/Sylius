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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncer;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Component\Core\Model\CatalogPromotion;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

class CatalogPromotionAnnouncerTest extends WebTestCase
{
    /** @var Client */
    private static $client;

    protected function setUp(): void
    {
        self::$client = static::createClient();
        self::$container = self::$client->getContainer();
    }

    /** @test */
    public function it_announces_catalog_promotion_has_been_created_and_updates_its_state_during_that_process(): void
    {
        /** @var CatalogPromotionAnnouncer $catalogPromotionAnnouncer */
        $catalogPromotionAnnouncer = self::$container->get('Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface');

        $catalogPromotionAnnouncer->dispatchCatalogPromotionCreatedEvent($this->getCatalogPromotion());

        /* @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.main');

        $this->assertCount(4, $transport->getSent());

        $this->assertInstanceOf(UpdateCatalogPromotionState::class, $transport->getSent()[0]->getMessage());
        $this->assertInstanceOf(CatalogPromotionCreated::class, $transport->getSent()[1]->getMessage());
        $this->assertInstanceOf(UpdateCatalogPromotionState::class, $transport->getSent()[2]->getMessage());
        $this->assertInstanceOf(CatalogPromotionEnded::class, $transport->getSent()[3]->getMessage());
    }

    /** @test */
    public function it_announces_catalog_promotion_has_been_updated_and_updates_its_state_during_that_process(): void
    {
        /** @var CatalogPromotionAnnouncer $catalogPromotionAnnouncer */
        $catalogPromotionAnnouncer = self::$container->get('Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface');

        $catalogPromotionAnnouncer->dispatchCatalogPromotionUpdatedEvent($this->getCatalogPromotion());

        /* @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.main');

        $this->assertCount(4, $transport->getSent());

        $this->assertInstanceOf(UpdateCatalogPromotionState::class, $transport->getSent()[0]->getMessage());
        $this->assertInstanceOf(CatalogPromotionUpdated::class, $transport->getSent()[1]->getMessage());
        $this->assertInstanceOf(UpdateCatalogPromotionState::class, $transport->getSent()[2]->getMessage());
        $this->assertInstanceOf(CatalogPromotionEnded::class, $transport->getSent()[3]->getMessage());
    }

    private function getCatalogPromotion(): CatalogPromotionInterface
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::$container->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtures = $fixtureLoader->load([__DIR__ . '/../DataFixtures/ORM/resources/catalog_promotions.yml'], [], [], PurgeMode::createDeleteMode());
        /** @var CatalogPromotion $catalogPromotion */
        $catalogPromotion = $fixtures['sale_1'];

        return $catalogPromotion;
    }
}
