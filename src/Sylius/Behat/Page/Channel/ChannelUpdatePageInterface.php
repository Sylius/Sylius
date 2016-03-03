<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Channel;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ChannelUpdatePageInterface
{
    /**
     * @param string $themeName
     */
    public function setTheme($themeName);

    public function unsetTheme();

    public function update();
}
