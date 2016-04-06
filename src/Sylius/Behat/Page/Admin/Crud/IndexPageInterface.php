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

use Sylius\Behat\Page\PageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface IndexPageInterface extends PageInterface
{
    /**
     * @return int
     */
    public function countAllResourcesOnPage();

    /**
     * @param array $parameters
     *
     * @return bool
     */
    public function isResourceOnPage(array $parameters);

    /**
     * @param array $parameters
     *
     * @return bool
     */
    public function deleteResourceOnPage(array $parameters);

    /**
     * @return int
     */
    public function countItems();
}
