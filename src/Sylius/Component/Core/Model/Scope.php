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

namespace Sylius\Component\Core\Model;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class Scope
{
    public const SHIPPING = 'shipping';
    public const TAX = 'tax';

    private function __construct()
    {
    }
}
