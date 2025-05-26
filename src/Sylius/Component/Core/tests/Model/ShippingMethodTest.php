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

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingMethod as BaseShippingMethod;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

final class ShippingMethodTest extends TestCase
{
    private ChannelInterface&MockObject $channel;

    private ShippingMethod $shippingMethod;

    protected function setUp(): void
    {
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->shippingMethod = new ShippingMethod();
    }

    public function testShouldImplementShippingMethodInterface(): void
    {
        $this->assertInstanceOf(ShippingMethodInterface::class, $this->shippingMethod);
    }

    public function testShouldExtendBaseShippingMethod(): void
    {
        $this->assertInstanceOf(BaseShippingMethod::class, $this->shippingMethod);
    }

    public function testShouldNotHaveAnyZoneDefinedByDefault(): void
    {
        $this->assertNull($this->shippingMethod->getZone());
    }

    public function testShouldZoneBeMutable(): void
    {
        $zone = $this->createMock(ZoneInterface::class);

        $this->shippingMethod->setZone($zone);

        $this->assertSame($zone, $this->shippingMethod->getZone());
    }

    public function testShouldTaxCategoryBeMutable(): void
    {
        $category = $this->createMock(TaxCategoryInterface::class);

        $this->shippingMethod->setTaxCategory($category);

        $this->assertSame($category, $this->shippingMethod->getTaxCategory());
    }

    public function testShouldInitializeChannelCollection(): void
    {
        $this->assertInstanceOf(Collection::class, $this->shippingMethod->getChannels());
    }

    public function testShouldChannelCollectionBeEmptyByDefault(): void
    {
        $this->assertTrue($this->shippingMethod->getChannels()->isEmpty());
    }

    public function testShouldAddChannel(): void
    {
        $this->shippingMethod->addChannel($this->channel);

        $this->assertTrue($this->shippingMethod->hasChannel($this->channel));
    }

    public function testShouldRemoveChannel(): void
    {
        $this->shippingMethod->addChannel($this->channel);

        $this->shippingMethod->removeChannel($this->channel);

        $this->assertFalse($this->shippingMethod->hasChannel($this->channel));
    }
}
