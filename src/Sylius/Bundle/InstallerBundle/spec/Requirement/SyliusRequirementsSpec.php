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

class SyliusRequirementsSpec extends ObjectBehavior
{
    public function let(RequirementCollection $requirementCollection)
    {
        $this->beConstructedWith(array($requirementCollection));
    }

    public function it_is_a_iterator_aggregate()
    {
        $this->shouldBeAnInstanceOf('IteratorAggregate');
    }

    public function it_gets_iterator()
    {
        $this->getIterator()->shouldHaveType('ArrayIterator');
    }

    public function its_add_should_have_fluent_interface($requirementCollection)
    {
        $this->add($requirementCollection)->shouldHaveType('Sylius\Bundle\InstallerBundle\Requirement\SyliusRequirements');
    }
}
