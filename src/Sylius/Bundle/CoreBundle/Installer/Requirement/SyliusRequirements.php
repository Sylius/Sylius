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

final class SyliusRequirements implements \IteratorAggregate
{
    /**
     * @var array|RequirementCollection[]
     */
    private $collections = [];

    /**
     * @param array|RequirementCollection[] $requirementCollections
     */
    public function __construct(array $requirementCollections)
    {
        foreach ($requirementCollections as $requirementCollection) {
            $this->add($requirementCollection);
        }
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->collections);
    }

    /**
     * @param RequirementCollection $collection
     */
    public function add(RequirementCollection $collection): void
    {
        $this->collections[] = $collection;
    }
}
