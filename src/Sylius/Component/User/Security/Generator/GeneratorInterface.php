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
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface GeneratorInterface
{
    /**
     * @return mixed
     */
    public function generate();
}
