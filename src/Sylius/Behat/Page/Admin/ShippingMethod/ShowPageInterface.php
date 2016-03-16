<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface ShowPageInterface extends PageInterface
{
    /**
     * @throws ElementNotFoundException
     */
    public function pressDelete();

    /**
     * @param string $message
     *
     * @return bool
     */
    public function flashContainsMessage($message);
}
