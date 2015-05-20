<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Manager;

use Sylius\Bundle\SettingsBundle\Model\Settings;

/**
 * Settings provider interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SettingsManagerInterface
{
    /**
     * Load settings from given namespace.
     *
     * @param string      $alias
     * @param null|string $namespace
     *
     * @return Settings
     */
    public function loadSettings($alias, $namespace = null);

    /**
     * Save settings under given namespace.
     *
     * @param string      $alias
     * @param null|string $namespace
     * @param Settings    $settings
     */
    public function saveSettings($alias, $namespace, Settings $settings);
}
