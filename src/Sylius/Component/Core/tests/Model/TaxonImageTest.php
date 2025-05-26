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

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Image;
use Sylius\Component\Core\Model\TaxonImage;

final class TaxonImageTest extends TestCase
{
    private TaxonImage $taxonImage;

    protected function setUp(): void
    {
        $this->taxonImage = new TaxonImage();
    }

    public function testShouldExtendImage(): void
    {
        $this->assertInstanceOf(Image::class, $this->taxonImage);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->taxonImage->getId());
    }

    public function testShouldNotHaveFileByDefault(): void
    {
        $this->assertFalse($this->taxonImage->hasFile());
        $this->assertNull($this->taxonImage->getFile());
    }

    public function testShouldFileBeMutable(): void
    {
        $file = new \SplFileInfo(__FILE__);
        $this->taxonImage->setFile($file);

        $this->assertSame($file, $this->taxonImage->getFile());
    }

    public function testShouldPathBeMutable(): void
    {
        $this->taxonImage->setPath(__FILE__);

        $this->assertSame(__FILE__, $this->taxonImage->getPath());
    }

    public function testShoulNotHaveTypeByDefault(): void
    {
        $this->assertNull($this->taxonImage->getType());
    }

    public function testShouldTypeBeMutable(): void
    {
        $this->taxonImage->setType('banner');

        $this->assertSame('banner', $this->taxonImage->getType());
    }

    public function testShouldNotHaveOwnerByDefault(): void
    {
        $this->assertNull($this->taxonImage->getOwner());
    }

    public function testShouldOwnerBeMutable(): void
    {
        $owner = new \stdClass();

        $this->taxonImage->setOwner($owner);

        $this->assertSame($owner, $this->taxonImage->getOwner());
    }
}
