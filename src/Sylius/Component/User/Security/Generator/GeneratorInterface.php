<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Security\Generator;

/**
* @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
*/
interface GeneratorInterface
{
    /**
     * Generates some random string
     * 
     * @param $length determines length of generated token 
     */
    public function generate($length);
}
