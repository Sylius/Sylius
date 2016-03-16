<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Loader;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface CircularDependencyCheckerInterface
{
    /**
     * @param ThemeInterface $theme
     *
     * @throws CircularDependencyFoundException
     */
    public function check(ThemeInterface $theme);
}
