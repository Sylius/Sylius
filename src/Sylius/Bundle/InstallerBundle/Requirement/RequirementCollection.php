<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\Requirement;

use IteratorAggregate;
use ArrayIterator;

class RequirementCollection implements IteratorAggregate
{
    protected $label;
    protected $requirements = array();

    public function __construct($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->requirements);
    }

    public function add(Requirement $requirement)
    {
        $this->requirements[] = $requirement;

        return $this;
    }
}
