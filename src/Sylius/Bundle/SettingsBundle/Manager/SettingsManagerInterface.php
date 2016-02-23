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

use Sylius\Bundle\SettingsBundle\Model\ParameterCollection;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SettingsManagerInterface
{
    /**
     * @param string $schemaAlias
     *
     * @return ParameterCollection
     */
    public function load($schemaAlias);

    /**
     * @param ParameterCollection $parameters
     */
    public function save(ParameterCollection $parameters);
}
