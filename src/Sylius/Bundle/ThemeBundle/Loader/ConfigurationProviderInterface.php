<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Loader;

/**
 * Provides configuration of all known themes, runs while building containers.
 * Instances of this interface can also implement CompilerPassInterface
 * to interfere with ContainerBuilder (e.g. add a FileResource)
 *
 * @see \Sylius\Bundle\ThemeBundle\DependencyInjection\Compiler\ThemeRepositoryPass
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ConfigurationProviderInterface
{
    /**
     * @return array
     */
    public function provideAll();
}
