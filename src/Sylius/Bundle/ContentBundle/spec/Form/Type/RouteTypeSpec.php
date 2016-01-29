<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ContentBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class RouteTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('My\Resource\Model', ['validation_group']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContentBundle\Form\Type\RouteType');
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('name', null, Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('parent', null, Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('content', 'sylius_static_content_choice', Argument::type('array'))->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_route');
    }
}
