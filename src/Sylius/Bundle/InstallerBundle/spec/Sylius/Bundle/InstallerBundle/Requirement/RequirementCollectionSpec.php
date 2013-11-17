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

class RequirementCollectionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('PHP version and settings');
    }

    function it_is_a_iterator_aggregate()
    {
        $this->shouldBeAnInstanceOf('IteratorAggregate');
    }

    function it_gets_label()
    {
        $this->getLabel()->shouldReturn('PHP version and settings');
    }

    function it_gets_iterator()
    {
        $this->getIterator()->shouldHaveType('ArrayIterator');
    }

    function its_add_should_have_fluent_interface(Requirement $requirement)
    {
        $this->add($requirement)->shouldHaveType('Sylius\Bundle\InstallerBundle\Requirement\RequirementCollection');
    }
}
