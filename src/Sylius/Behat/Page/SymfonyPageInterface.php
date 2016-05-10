<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface SymfonyPageInterface extends PageInterface
{
    /**
     * @return string
     */
    public function getRouteName();
}
