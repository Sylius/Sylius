<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Resolver;

use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
interface SettingsResolverInterface
{
    /**
     * Resolves settings based on schema and an optional context array.
     *
     * @param string $schema
     *
     * @return SettingsInterface
     */
    public function resolve($schema);
}
