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

namespace Tests\Sylius\Behat\Service\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Behat\Service\Generator\UploadedImagePathGenerator;
use Sylius\Component\Core\Generator\ImagePathGeneratorInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadedImagePathGeneratorTest extends TestCase
{
    private UploadedImagePathGenerator $uploadedImagePathGenerator;

    protected function setUp(): void
    {
        $this->uploadedImagePathGenerator = new UploadedImagePathGenerator();
    }

    public function testImplementsImagePathGeneratorInterface(): void
    {
        $this->assertInstanceOf(ImagePathGeneratorInterface::class, $this->uploadedImagePathGenerator);
    }

    public function testGeneratesRandomHashedPathKeepingTheImageName(): void
    {
        /** @var ImageInterface&MockObject $image */
        $image = $this->createMock(ImageInterface::class);

        $file = new UploadedFile(__DIR__ . '/ford.jpg', 'ford.jpg', null, null, true);
        $image->expects($this->once())->method('getFile')->willReturn($file);

        $this->assertMatchesRegularExpression(
            '/[a-z0-9]{2}\/[a-z0-9]{2}\/[a-zA-Z0-9]+[_-]*\/ford[.]jpg/i',
            $this->uploadedImagePathGenerator->generate($image),
        );
    }
}
