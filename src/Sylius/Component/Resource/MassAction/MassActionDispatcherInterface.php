<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\MassAction;

interface MassActionDispatcherInterface
{
    /**
     * Dispatch action of given type.
     *
     * @param $type
     * @param array $resources
     */
    public function dispatch($type, array $resources);
}
