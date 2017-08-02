<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Configuration;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface ConfigurationProviderInterface
{
    /**
     * @return array
     */
    public function getConfigurations();
}
