<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

/**
 * Handle the authorization process on resources.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
interface AuthorizationHandlerInterface
{
    const ACTION_INDEX = 'index';
    const ACTION_SHOW = 'show';
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    /**
     * Check authorization on a resource for the given action.
     *
     * @param string $action
     */
    public function checkAuthorization($action);

    /**
     * Returns the list of all available actions on resources.
     *
     * @return array
     */
    public function getAvailableActions();
}