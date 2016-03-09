<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Crud;

use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface EditPageInterface extends PageInterface
{
    /**
     * @param string $name
     *
     * @throws ElementNotFoundException
     */
    public function fillName($name);

    /**
     * @throws ElementNotFoundException
     */
    public function saveChanges();
}
