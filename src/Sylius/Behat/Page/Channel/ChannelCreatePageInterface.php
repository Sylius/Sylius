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

use Sylius\Behat\Page\PageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ChannelCreatePageInterface extends PageInterface
{
    /**
     * @param string $name
     */
    public function fillName($name);

    /**
     * @param string $code
     */
    public function fillCode($code);

    public function create();
}
