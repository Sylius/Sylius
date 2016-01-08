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
use Sylius\Bundle\InstallerBundle\Requirement\RequirementCollection;
use Sylius\Bundle\InstallerBundle\Requirement\SyliusRequirements;

class SyliusRequirementsSpec extends ObjectBehavior
{
    function let(RequirementCollection $requirementCollection)
    {
        $this->beConstructedWith([$requirementCollection]);
    }

    function it_is_a_iterator_aggregate()
    {
        $this->shouldBeAnInstanceOf(\IteratorAggregate::class);
    }

    function it_gets_iterator()
    {
        $this->getIterator()->shouldHaveType(\ArrayIterator::class);
    }

    function its_add_should_have_fluent_interface($requirementCollection)
    {
        $this->add($requirementCollection)->shouldHaveType(SyliusRequirements::class);
    }
}
