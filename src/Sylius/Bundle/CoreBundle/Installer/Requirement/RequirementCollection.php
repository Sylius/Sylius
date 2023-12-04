<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Installer\Requirement;

/**
 * @implements \IteratorAggregate<Requirement>
 */
abstract class RequirementCollection implements \IteratorAggregate
{
    /** @var array|Requirement[] */
    protected $requirements = [];

    public function __construct(protected string $label)
    {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->requirements);
    }

    public function add(Requirement $requirement): self
    {
        $this->requirements[] = $requirement;

        return $this;
    }
}
