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

namespace Tests\Sylius\Component\Shipping\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Shipping\Model\ShippingMethodTranslation;
use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;

final class ShippingMethodTranslationTest extends TestCase
{
    private ShippingMethodTranslation $shippingMethodTranslation;

    protected function setUp(): void
    {
        $this->shippingMethodTranslation = new ShippingMethodTranslation();
    }

    public function testShouldBeInitializable(): void
    {
        $this->assertInstanceOf(ShippingMethodTranslation::class, $this->shippingMethodTranslation);
    }

    public function testShouldImplementShippingMethodTranslationInterface(): void
    {
        $this->assertInstanceOf(ShippingMethodTranslationInterface::class, $this->shippingMethodTranslation);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->shippingMethodTranslation->getId());
    }

    public function testShouldBeUnnamedByDefault(): void
    {
        $this->assertNull($this->shippingMethodTranslation->getName());
    }

    public function testShouldNameBeMutable(): void
    {
        $this->shippingMethodTranslation->setName('Shippable goods');

        $this->assertSame('Shippable goods', $this->shippingMethodTranslation->getName());
    }

    public function testShouldDescriptionBeMutable(): void
    {
        $this->shippingMethodTranslation->setDescription('Very good shipping, cheap price, good delivery time.');

        $this->assertSame(
            'Very good shipping, cheap price, good delivery time.',
            $this->shippingMethodTranslation->getDescription(),
        );
    }

    public function testShouldReturnNameWhenConvertedToString(): void
    {
        $this->shippingMethodTranslation->setName('Shippable goods');

        $this->assertSame('Shippable goods', (string) $this->shippingMethodTranslation);
    }
}
