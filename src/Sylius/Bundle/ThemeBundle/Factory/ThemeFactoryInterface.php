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

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeFactoryInterface
{
    /**
     * @param array $themeData
     *
     * @return ThemeInterface
     *
     * @throws \InvalidArgumentException If factory is unable to create theme instance out of given data
     */
    public function createFromArray(array $themeData);
}
