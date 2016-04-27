<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Helper;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
interface PathHelperInterface
{
    /**
     * @return array
     *
     * @throws InvalidConfigurationException If not allowed to make context aware paths.
     */
    public function applySuffixFor(array $paths = []);
}
