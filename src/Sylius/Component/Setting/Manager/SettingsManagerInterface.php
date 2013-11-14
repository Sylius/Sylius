<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Setting\Manager;

use Sylius\Component\Setting\Model\Settings;

/**
 * Settings provider interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface SettingsManagerInterface
{
    /**
     * Load settings from given namespace.
     *
     * @param string $namespace
     *
     * @return Settings
     */
    public function loadSettings($namespace);

    /**
     * Save settings under given namespace.
     *
     * @param string   $namespace
     * @param Settings $settings
     */
    public function saveSettings($namespace, Settings $settings);
}
