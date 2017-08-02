<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Grid\Definition;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ActionGroup
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Action[]
     */
    private $actions = [];

    /**
     * @param string $name
     */
    private function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public static function named($name)
    {
        return new self($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param Action $action
     */
    public function addAction(Action $action)
    {
        if ($this->hasAction($name = $action->getName())) {
            throw new \InvalidArgumentException(sprintf('Action "%s" already exists.', $name));
        }

        $this->actions[$name] = $action;
    }

    /**
     * @param string $name
     *
     * @return Action
     */
    public function getAction($name)
    {
        if (!$this->hasAction($name)) {
            throw new \InvalidArgumentException(sprintf('Action "%s" does not exist.', $name));
        }

        return $this->actions[$name];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAction($name)
    {
        return isset($this->actions[$name]);
    }
}
