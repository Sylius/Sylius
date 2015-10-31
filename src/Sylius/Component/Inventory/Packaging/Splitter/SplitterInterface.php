<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Packaging\Splitter;

/**
 * Splitter services can create several separate packages from a single location.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SplitterInterface
{
    /**
     * Split packages if necessary.
     *
     * @param PackageInterface[] $packages
     *
     * @return Packageinterface[]
     */
    public function split(array $packages);
}
