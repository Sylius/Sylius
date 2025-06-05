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

namespace Tests\Sylius\Bundle\CoreBundle\Fixture\Listener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\Listener\ImagesPurgerListener;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;
use Symfony\Component\Filesystem\Filesystem;

final class ImagesPurgerListenerTest extends TestCase
{
    private Filesystem&MockObject $filesystem;

    private ImagesPurgerListener $imagesPurgerListener;

    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->imagesPurgerListener = new ImagesPurgerListener($this->filesystem, '/media');
    }

    public function testRemovesImagesBeforeFixtureSuite(): void
    {
        $suite = $this->createMock(SuiteInterface::class);

        $this->filesystem->expects($this->once())->method('remove')->with('/media');
        $this->filesystem->expects($this->once())->method('mkdir')->with('/media');
        $this->filesystem->expects($this->once())->method('touch')->with('/media/.gitkeep');

        $this->imagesPurgerListener->beforeSuite(new SuiteEvent($suite), []);
    }
}
