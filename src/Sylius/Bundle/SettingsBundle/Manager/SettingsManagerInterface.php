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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SettingsManagerInterface
{
    /**
     * @param string $schemaAlias
     * @param string|null $namespace
     *
     * @return SettingsInterface
     */
    public function load($schemaAlias, $namespace = null);

    /**
     * @param SettingsInterface $settings
     */
    public function save(SettingsInterface $settings);
}
