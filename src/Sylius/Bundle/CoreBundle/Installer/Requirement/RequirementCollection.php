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

final class RequirementCollection implements IteratorAggregate
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var Requirement[]
     */
    private $requirements = [];

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
     */
    public function add(Requirement $requirement)
    {
        $this->requirements[] = $requirement;
    }
}
