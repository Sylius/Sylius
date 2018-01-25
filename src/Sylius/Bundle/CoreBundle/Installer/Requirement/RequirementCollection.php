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

namespace Sylius\Bundle\CoreBundle\Installer\Requirement;

abstract class RequirementCollection implements \IteratorAggregate
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
    public function __construct(string $label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->requirements);
    }

    /**
     * @param Requirement $requirement
     *
     * @return RequirementCollection
     */
    public function add(Requirement $requirement): self
    {
        $this->requirements[] = $requirement;

        return $this;
    }
}
