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

namespace Tests\Sylius\Component\Taxonomy\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

final class TaxonTranslationTest extends TestCase
{
    private TaxonTranslation $taxonTranslation;

    protected function setUp(): void
    {
        $this->taxonTranslation = new TaxonTranslation();
    }

    public function testShouldImplementTaxonTranslationInterface(): void
    {
        $this->assertInstanceOf(TaxonTranslationInterface::class, $this->taxonTranslation);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        $this->assertNull($this->taxonTranslation->getId());
    }

    public function testShouldBeUnnamedByDefault(): void
    {
        $this->assertNull($this->taxonTranslation->getName());
    }

    public function testShouldNameBeMutable(): void
    {
        $this->taxonTranslation->setName('Brand');

        $this->assertEquals('Brand', $this->taxonTranslation->getName());
    }

    public function testShouldReturnNameWhenConvertedToString(): void
    {
        $this->taxonTranslation->setName('Brand');

        $this->assertEquals('Brand', (string) $this->taxonTranslation);
    }

    public function testShouldHaveNoDescriptionByDefault(): void
    {
        $this->assertNull($this->taxonTranslation->getDescription());
    }

    public function testShouldDescriptionBeMutable(): void
    {
        $this->taxonTranslation->setDescription('This is a list of brands.');

        $this->assertEquals('This is a list of brands.', $this->taxonTranslation->getDescription());
    }

    public function testShouldHaveNoSlugByDefault(): void
    {
        $this->assertNull($this->taxonTranslation->getSlug());
    }

    public function testShouldSlugBeMutable(): void
    {
        $this->taxonTranslation->setSlug('t-shirts');

        $this->assertEquals('t-shirts', $this->taxonTranslation->getSlug());
    }
}
