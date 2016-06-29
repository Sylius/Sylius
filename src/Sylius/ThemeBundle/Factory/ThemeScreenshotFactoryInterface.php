<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ThemeBundle\Factory;

use Sylius\ThemeBundle\Model\ThemeScreenshot;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeScreenshotFactoryInterface
{
    /**
     * @param array $data
     *
     * @return ThemeScreenshot
     */
    public function createFromArray(array $data);
}
