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

namespace Tests\Sylius\Component\Promotion\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Model\PromotionTranslation;
use Sylius\Component\Promotion\Model\PromotionTranslationInterface;

final class PromotionTranslationTest extends TestCase
{
    private PromotionTranslation $promotionTranslation;

    protected function setUp(): void
    {
        $this->promotionTranslation = new PromotionTranslation();
    }

    public function testShouldBeInitializable(): void
    {
        $this->assertInstanceOf(PromotionTranslation::class, $this->promotionTranslation);
    }

    public function testShouldImplementCatalogPromotionTranslationInterface(): void
    {
        $this->assertInstanceOf(PromotionTranslationInterface::class, $this->promotionTranslation);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->promotionTranslation->getId());
    }

    public function testShouldLabelBeMutable(): void
    {
        $this->promotionTranslation->setLabel('Mugs discount');

        $this->assertEquals('Mugs discount', $this->promotionTranslation->getLabel());
    }
}
