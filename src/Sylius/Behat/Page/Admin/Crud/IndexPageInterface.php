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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface IndexPageInterface extends PageInterface
{
    /**
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    public function isSuccessfulMessage();

    /**
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    public function isSuccessfullyCreated();

    /**
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    public function isSuccessfullyUpdated();

    /**
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    public function isSuccessfullyDeleted();

    /**
     * @param array $parameters
     *
     * @return bool
     */
    public function isResourceAppearInTheStoreBy(array $parameters);

    /**
     * @param string $message
     *
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    public function hasMessage($message);
}
