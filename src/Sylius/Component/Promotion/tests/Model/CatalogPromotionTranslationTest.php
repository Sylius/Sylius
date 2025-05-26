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
use Sylius\Component\Promotion\Model\CatalogPromotionTranslation;
use Sylius\Component\Promotion\Model\CatalogPromotionTranslationInterface;

final class CatalogPromotionTranslationTest extends TestCase
{
    private CatalogPromotionTranslation $catalogPromotionTranslation;

    protected function setUp(): void
    {
        $this->catalogPromotionTranslation = new CatalogPromotionTranslation();
    }

    public function testShouldBeCatalogPromotionTranslation(): void
    {
        $this->assertInstanceOf(CatalogPromotionTranslation::class, $this->catalogPromotionTranslation);
    }

    public function testShouldImplementCatalogPromotionTranslationInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionTranslationInterface::class, $this->catalogPromotionTranslation);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->catalogPromotionTranslation->getId());
    }

    public function testShouldLabelBeMutable(): void
    {
        $this->catalogPromotionTranslation->setLabel('Mugs discount');

        $this->assertSame('Mugs discount', $this->catalogPromotionTranslation->getLabel());
    }

    public function testShouldDescriptionBeMutable(): void
    {
        $this->catalogPromotionTranslation->setDescription('Discount on every mug.');

        $this->assertSame('Discount on every mug.', $this->catalogPromotionTranslation->getDescription());
    }
}
