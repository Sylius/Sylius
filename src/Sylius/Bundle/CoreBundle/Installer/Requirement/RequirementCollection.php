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
    /** @var string */
    protected $label;

    /** @var Requirement[] */
    protected $requirements = [];

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->requirements);
    }

    /**
     * @return RequirementCollection
     */
    public function add(Requirement $requirement): self
    {
        $this->requirements[] = $requirement;

        return $this;
    }
}
