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

namespace Tests\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\CatalogPromotion;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class CatalogPromotionTest extends TestCase
{
    private ChannelInterface $channel;

    private CatalogPromotion $catalogPromotion;

    protected function setUp(): void
    {
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->catalogPromotion = new CatalogPromotion();
    }

    public function testShouldImplementCatalogPromotionInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionInterface::class, $this->catalogPromotion);
    }

    public function testShouldHaveChannelsCollection(): void
    {
        $secondChannel = $this->createMock(ChannelInterface::class);
        $this->catalogPromotion->addChannel($this->channel);
        $this->catalogPromotion->addChannel($secondChannel);

        $this->assertEquals(new ArrayCollection([$this->channel, $secondChannel]), $this->catalogPromotion->getChannels());
    }

    public function testShouldAddChannel(): void
    {
        $this->catalogPromotion->addChannel($this->channel);

        $this->assertTrue($this->catalogPromotion->hasChannel($this->channel));
    }

    public function testShouldRemoveChannel(): void
    {
        $this->catalogPromotion->addChannel($this->channel);

        $this->catalogPromotion->removeChannel($this->channel);

        $this->assertFalse($this->catalogPromotion->hasChannel($this->channel));
    }
}
