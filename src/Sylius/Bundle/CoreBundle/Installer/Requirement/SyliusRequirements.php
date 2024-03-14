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
 * @implements \IteratorAggregate<RequirementCollection>
 */
final class SyliusRequirements implements \IteratorAggregate
{
    /** @var array|RequirementCollection[] */
    private array $collections = [];

    /**
     * @param array|RequirementCollection[] $requirementCollections
     */
    public function __construct(array $requirementCollections)
    {
        foreach ($requirementCollections as $requirementCollection) {
            $this->add($requirementCollection);
        }
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->collections);
    }

    public function add(RequirementCollection $collection): void
    {
        $this->collections[] = $collection;
    }
}
