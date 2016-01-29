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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SettingsManagerInterface
{
    /**
     * @param string $namespace
     *
     * @return Settings
     */
    public function loadSettings($namespace);

    /**
     * @param string   $namespace
     * @param Settings $settings
     */
    public function saveSettings($namespace, Settings $settings);
}
