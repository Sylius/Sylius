<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Generator;

use Sylius\Component\Core\Model\ImageInterface;

final class UploadedImagePathGenerator implements ImagePathGeneratorInterface
{
    public function generate(ImageInterface $image): string
    {
        $file = $image->getFile();

        $hash = bin2hex(random_bytes(16));

        return $this->expandPath($hash . '.' . $file->guessExtension());
    }

    private function expandPath(string $path): string
    {
        return sprintf('%s/%s/%s', substr($path, 0, 2), substr($path, 2, 2), substr($path, 4));
    }
}
