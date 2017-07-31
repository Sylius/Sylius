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

namespace Sylius\Component\Resource;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourceActions
{
    public const SHOW = 'show';
    public const INDEX = 'index';
    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    private function __construct()
    {
    }
}
