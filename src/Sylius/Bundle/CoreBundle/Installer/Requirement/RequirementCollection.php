<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Installer\Requirement;

use ArrayIterator;
use IteratorAggregate;

abstract class RequirementCollection implements IteratorAggregate
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var Requirement[]
     */
    protected $requirements = [];

    /**
     * @param string $label
     */
    public function __construct($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->requirements);
    }

    /**
     * @param Requirement $requirement
     * @return RequirementCollection
     */
    public function add(Requirement $requirement)
    {
        $this->requirements[] = $requirement;

        return $this;
    }
}
