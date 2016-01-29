<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InstallerBundle\Requirement;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\InstallerBundle\Requirement\Requirement;
use Sylius\Bundle\InstallerBundle\Requirement\RequirementCollection;

class RequirementCollectionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('PHP Version and Settings');
    }

    function it_is_a_iterator_aggregate()
    {
        $this->shouldBeAnInstanceOf(\IteratorAggregate::class);
    }

    function it_gets_label()
    {
        $this->getLabel()->shouldReturn('PHP Version and Settings');
    }

    function it_gets_iterator()
    {
        $this->getIterator()->shouldHaveType(\ArrayIterator::class);
    }

    function its_add_should_have_fluent_interface(Requirement $requirement)
    {
        $this->add($requirement)->shouldHaveType(RequirementCollection::class);
    }
}
