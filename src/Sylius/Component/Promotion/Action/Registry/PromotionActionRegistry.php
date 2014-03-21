<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Action\Registry;

use Sylius\Component\Promotion\Action\PromotionActionInterface;

/**
 * Promotion action registry.
 *
 * This service keeps all promotion actions registered inside
 * container. Allows to retrieve them by type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionActionRegistry implements PromotionActionRegistryInterface
{
    /**
     * Promotion actions.
     *
     * @var PromotionActionInterface[]
     */
    protected $actions = array();

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * {@inheritdoc}
     */
    public function registerAction($name, PromotionActionInterface $action)
    {
        if ($this->hasAction($name)) {
            throw new ExistingPromotionActionException($name);
        }

        $this->actions[$name] = $action;
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterAction($name)
    {
        if (!$this->hasAction($name)) {
            throw new NonExistingPromotionActionException($name);
        }

        unset($this->actions[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAction($name)
    {
        return isset($this->actions[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAction($name)
    {
        if (!$this->hasAction($name)) {
            throw new NonExistingPromotionActionException($name);
        }

        return $this->actions[$name];
    }
}
