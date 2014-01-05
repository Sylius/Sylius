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
class AuthorizationHandler implements AuthorizationHandlerInterface
{
    /**
     * @var ResourceController
     */
    private $controller;

    public function __construct(ResourceController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @inheritdoc
     */
    public function checkAuthorization($action)
    {
        if (!in_array($action, $this->getAvailableActions())) {
            throw new \Exception(sprintf('Unknown action %s', $action));
        }

        $method = sprintf('check%sAuthorization', ucfirst($action));

        if (method_exists($this->controller, $method)) {
            $this->controller->$method();
        }
    }

    /**
     * @inheritdoc
     */
    public function getAvailableActions()
    {
        return array(
            AuthorizationHandlerInterface::ACTION_CREATE,
            AuthorizationHandlerInterface::ACTION_DELETE,
            AuthorizationHandlerInterface::ACTION_UPDATE,
            AuthorizationHandlerInterface::ACTION_INDEX,
            AuthorizationHandlerInterface::ACTION_SHOW,
        );
    }
} 