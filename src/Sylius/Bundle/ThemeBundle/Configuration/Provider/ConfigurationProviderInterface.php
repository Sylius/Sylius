<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Configuration\Provider;

use Symfony\Component\Config\Resource\ResourceInterface;

/**
 * Provides configuration of all known themes, runs while building the container.
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ConfigurationProviderInterface
{
    /**
     * @return array
     */
    public function getConfigurations();

    /**
     * Used for cache regenerating.
     *
     * @return ResourceInterface[]
     */
    public function getResources();
}
