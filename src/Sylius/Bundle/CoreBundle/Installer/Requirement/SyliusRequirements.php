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

final class SyliusRequirements implements IteratorAggregate
{
    /**
     * @var RequirementCollection[]
     */
    private $collections = [];

    /**
     * @param RequirementCollection[] $requirementCollections
     */
    public function __construct(array $requirementCollections)
    {
        foreach ($requirementCollections as $requirementCollection) {
            $this->add($requirementCollection);
        }
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->collections);
    }

    /**
     * @param RequirementCollection $collection
     */
    public function add(RequirementCollection $collection)
    {
        $this->collections[] = $collection;
    }
}
