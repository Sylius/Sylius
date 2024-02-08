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

namespace Sylius\Tests\Functional;

use PHPUnit\Framework\Assert;
use Sylius\Component\Core\Model\ProductImage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\BrowserKit\Client;

final class ImageUploaderTest extends WebTestCase
{
    /** @var Client */
    private static $client;

    /** @test */
    public function it_sanitizes_file_content_if_it_is_svg_mime_type(): void
    {
        self::$client = static::createClient();

        $imageUploader = self::$kernel->getContainer()->get('sylius.image_uploader');
        $fileSystem =  self::$kernel->getContainer()->get('gaufrette.sylius_image_filesystem');

        $file = new UploadedFile(__DIR__ . '/../Resources/xss.svg', 'xss.svg');
        Assert::assertStringContainsString('<script', $this->getContent($file));

        $image = new ProductImage();
        $image->setFile($file);

        $imageUploader->upload($image);

        $sanitizedFile = $fileSystem->get($image->getPath());
        Assert::assertStringNotContainsString('<script', $sanitizedFile->getContent());
    }

    private function getContent(UploadedFile $file): string
    {
        $content = file_get_contents($file->getPathname());

        if (false === $content) {
            throw new FileException(sprintf('Could not get the content of the file "%s".', $file->getPathname()));
        }

        return $content;
    }
}
