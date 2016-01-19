<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Context;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeContextInterface
{
    /**
     * @param ThemeInterface $theme
     */
    public function setTheme(ThemeInterface $theme);

    /**
     * @return ThemeInterface|null
     */
    public function getTheme();

    /**
     * @return ThemeInterface[]
     */
    public function getThemeHierarchy();
}
