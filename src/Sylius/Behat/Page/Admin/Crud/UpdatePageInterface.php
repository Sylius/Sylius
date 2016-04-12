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
interface UpdatePageInterface extends PageInterface, PageWithFormInterface
{
    public function saveChanges();

    /**
     * @param array $parameters where keys are some of arbitrary elements defined by user and values are expected values
     *
     * @return bool
     */
    public function hasResourceValues(array $parameters);
}
