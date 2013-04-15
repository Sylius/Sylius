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

class SyliusRequirements implements IteratorAggregate
{
    protected $collections = array();

    public function __construct(array $requirementCollections)
    {
        foreach ($requirementCollections as $requirementCollection) {
            $this->add($requirementCollection);
        }
    }

    public function getIterator()
    {
        return new ArrayIterator($this->collections);
    }

    public function add(RequirementCollection $collection)
    {
        $this->collections[] = $collection;

        return $this;
    }
}
