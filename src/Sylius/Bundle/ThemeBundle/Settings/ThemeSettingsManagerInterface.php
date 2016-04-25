<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Settings;

use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeSettingsManagerInterface
{
    /**
     * @param ThemeInterface $theme
     * @param string|null $namespace
     *
     * @return SettingsInterface
     */
    public function load(ThemeInterface $theme, $namespace = null);

    /**
     * @param SettingsInterface $settings
     */
    public function save(SettingsInterface $settings);
}
