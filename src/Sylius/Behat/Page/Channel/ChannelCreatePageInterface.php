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
interface ChannelCreatePageInterface extends PageInterface
{
    /**
     * @param string $name
     *
     * @throws ElementNotFoundException
     */
    public function fillName($name);

    /**
     * @param string $code
     *
     * @throws ElementNotFoundException
     */
    public function fillCode($code);

    /**
     * @throws ElementNotFoundException
     */
    public function create();
}
