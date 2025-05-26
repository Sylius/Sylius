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

namespace Tests\Sylius\Component\Core\Generator;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Generator\ImagePathGeneratorInterface;
use Sylius\Component\Core\Generator\UploadedImagePathGenerator;
use Sylius\Component\Core\Model\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadedImagePathGeneratorTest extends TestCase
{
    private UploadedImagePathGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new UploadedImagePathGenerator();
    }

    public function testShouldImplementImagePathGeneratorInterface(): void
    {
        $this->assertInstanceOf(ImagePathGeneratorInterface::class, $this->generator);
    }

    public function testShouldGenerateRandomHashedPathForTheImage(): void
    {
        $image = $this->createMock(ImageInterface::class);
        $file = new UploadedFile(__DIR__ . '/ford.jpg', 'ford.jpg', null, null, true);

        $image->expects($this->once())->method('getFile')->willReturn($file);

        $this->assertMatchesRegularExpression(
            '/[a-z0-9]{2}\/[a-z0-9]{2}\/[a-zA-Z0-9]+[_-]*[.]jpe?g/i',
            $this->generator->generate($image),
        );
    }
}
