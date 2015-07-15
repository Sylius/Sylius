<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

class GroupTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('Group', array('sylius'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\Type\GroupType');
    }

    public function it_is_a_form()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    public function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('name', 'text', Argument::type('array'))->shouldbeCalled()->willReturn($builder);

        $this->buildForm($builder);
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_group');
    }
}
