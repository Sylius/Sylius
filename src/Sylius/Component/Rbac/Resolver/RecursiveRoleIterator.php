<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Resolver;

use ArrayIterator;
use Doctrine\Common\Collections\Collection;
use RecursiveIterator;
use Sylius\Component\Rbac\Model\RoleInterface;

class RecursiveRoleIterator extends ArrayIterator implements RecursiveIterator
{
    public function __construct($roles)
    {
        if ($roles instanceof Collection) {
            $roles = $roles->toArray();
        }

        parent::__construct($roles);
    }

    public function valid()
    {
        return $this->current() instanceof RoleInterface;
    }

    public function hasChildren()
    {
        return $this->current()->hasChildren();
    }

    public function getChildren()
    {
        return new self($this->current()->getChildren());
    }
}
