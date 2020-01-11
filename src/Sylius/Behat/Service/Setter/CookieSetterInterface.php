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

namespace Sylius\Behat\Service\Setter;

interface CookieSetterInterface
{
    /**
     * @param string $name
     * @param string $value
     */
    public function setCookie($name, $value);
}
