<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Factory;

use Sylius\Bundle\ThemeBundle\Model\ThemeScreenshot;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeScreenshotFactory implements ThemeScreenshotFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $data)
    {
        Assert::keyExists($data, 'path');

        $themeScreenshot = new ThemeScreenshot($data['path']);

        if (isset($data['title'])) {
            $themeScreenshot->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $themeScreenshot->setDescription($data['description']);
        }

        return $themeScreenshot;
    }
}
