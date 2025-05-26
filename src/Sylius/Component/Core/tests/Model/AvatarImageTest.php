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
use Sylius\Component\Core\Model\AvatarImage;
use Sylius\Component\Core\Model\Image;

final class AvatarImageTest extends TestCase
{
    private AvatarImage $avatarImage;

    protected function setUp(): void
    {
        $this->avatarImage = new AvatarImage();
    }

    public function testShouldExtendImage(): void
    {
        $this->assertInstanceOf(Image::class, $this->avatarImage);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->avatarImage->getId());
    }

    public function testShouldNotHaveFileByDefault(): void
    {
        $this->assertFalse($this->avatarImage->hasFile());
        $this->assertNull($this->avatarImage->getFile());
    }

    public function testShouldFileBeMutable(): void
    {
        $file = new \SplFileInfo(__FILE__);
        $this->avatarImage->setFile($file);

        $this->assertSame($file, $this->avatarImage->getFile());
    }

    public function testShouldPathBeMutable(): void
    {
        $this->avatarImage->setPath(__FILE__);

        $this->assertSame(__FILE__, $this->avatarImage->getPath());
    }

    public function testShouldNotHaveTypeByDefault(): void
    {
        $this->assertNull($this->avatarImage->getType());
    }

    public function testShouldTypeBeMutable(): void
    {
        $this->avatarImage->setType('banner');

        $this->assertSame('banner', $this->avatarImage->getType());
    }

    public function testShouldNotHaveOwnerByDefault(): void
    {
        $this->assertNull($this->avatarImage->getOwner());
    }

    public function testShouldOwnerBeMutable(): void
    {
        $owner = new \stdClass();

        $this->avatarImage->setOwner($owner);

        $this->assertSame($owner, $this->avatarImage->getOwner());
    }
}
