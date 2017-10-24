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

namespace Sylius\Bundle\ThemeBundle\Factory;

use Sylius\Bundle\ThemeBundle\Model\ThemeScreenshot;

final class ThemeScreenshotFactory implements ThemeScreenshotFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $data): ThemeScreenshot
    {
        if (!array_key_exists('path', $data)) {
            throw new \InvalidArgumentException('Screenshot path is required.');
        }

        $themeScreenshot = new ThemeScreenshot($data['path']);
        $themeScreenshot->setTitle($data['title'] ?? null);
        $themeScreenshot->setDescription($data['description'] ?? null);

        return $themeScreenshot;
    }
}
