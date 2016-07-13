<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Factory;

use Sylius\Bundle\ThemeBundle\Model\ThemeScreenshot;

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
