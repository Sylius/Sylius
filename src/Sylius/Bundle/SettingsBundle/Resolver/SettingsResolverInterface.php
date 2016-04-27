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
 * A settings resolver is always bound to only one schema alias. You can change the way how that schema will resolve by
 * implementing this interface. You could for example implement a resolver that uses a query parameter on the
 * current request.
 *
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
interface SettingsResolverInterface
{
    /**
     * @param string $schemaAlias
     * @param string|null $namespace
     *
     * @return SettingsInterface
     */
    public function resolve($schemaAlias, $namespace = null);
}
