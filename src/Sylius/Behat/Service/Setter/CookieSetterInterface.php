<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service\Setter;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface CookieSetterInterface
{
    /**
     * @param string $name
     * @param string $value
     */
    public function setCookie($name, $value);
}
