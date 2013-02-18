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

use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;

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
     * @return SettingsInterface
     */
    public function loadSettings($namespace);

    /**
     * Save settings under given namespace.
     *
     * @param string            $namespace
     * @param SettingsInterface $settings
     */
    public function saveSettings($namespace, SettingsInterface $settings);
}
