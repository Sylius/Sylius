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
     * @return bool
     */
    public function hasSuccessMessage();

    /**
     * @return bool
     */
    public function isSuccessfullyCreated();

    /**
     * @return bool
     */
    public function isSuccessfullyUpdated();

    /**
     * @return bool
     */
    public function isSuccessfullyDeleted();

    /**
     * @param array $parameters
     *
     * @return bool
     */
    public function isResourceOnPage(array $parameters);

    /**
     * @param string $message
     *
     * @return bool
     */
    public function hasMessage($message);
}
