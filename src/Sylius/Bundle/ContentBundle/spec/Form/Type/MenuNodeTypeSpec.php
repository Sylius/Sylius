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
class MenuNodeTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('My\Resource\Model', array('validation_group'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContentBundle\Form\Type\MenuNodeType');
    }

    public function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('name', 'text', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('label', 'text', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('display', null, Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('displayChildren', null, Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('linkType', 'choice', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('publishable', null, Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('publishStartDate', 'datetime', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('publishEndDate', 'datetime', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('route', null, Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('content', 'sylius_static_content_choice',  Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('uri', null, Argument::type('array'))->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder);
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_menu_node');
    }
}
