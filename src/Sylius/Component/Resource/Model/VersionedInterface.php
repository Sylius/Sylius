<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Model;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface VersionedInterface
{
    /**
     * @return int
     */
    public function getVersion();
}
