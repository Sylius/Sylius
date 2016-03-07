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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ChannelUpdatePageInterface extends PageInterface
{
    /**
     * @param string $themeName
     */
    public function setTheme($themeName);

    /**
     * @throws ElementNotFoundException
     */
    public function unsetTheme();

    /**
     * @throws ElementNotFoundException
     */
    public function update();
}
