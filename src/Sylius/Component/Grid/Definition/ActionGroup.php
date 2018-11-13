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

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function named(string $name): self
    {
        return new self($name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function addAction(Action $action): void
    {
        if ($this->hasAction($name = $action->getName())) {
            throw new \InvalidArgumentException(sprintf('Action "%s" already exists.', $name));
        }

        $this->actions[$name] = $action;
    }

    public function getAction(string $name): Action
    {
        if (!$this->hasAction($name)) {
            throw new \InvalidArgumentException(sprintf('Action "%s" does not exist.', $name));
        }

        return $this->actions[$name];
    }

    public function hasAction(string $name): bool
    {
        return isset($this->actions[$name]);
    }
}
