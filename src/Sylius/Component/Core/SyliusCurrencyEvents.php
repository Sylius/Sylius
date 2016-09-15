<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class SyliusCurrencyEvents
{
    const CODE_CHANGED = 'sylius.currency.code_changed';

    private function __construct()
    {
    }
}
