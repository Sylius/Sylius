<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourceActions
{
    const SHOW       = 'show';
    const INDEX      = 'index';
    const GRID       = 'grid';
    const CREATE     = 'create';
    const UPDATE     = 'update';
    const DELETE     = 'delete';
    const RESTORE    = 'restore';
    const REVERT     = 'revert';
    const TRANSITION = 'transition';
    const MOVE       = 'move';
}
