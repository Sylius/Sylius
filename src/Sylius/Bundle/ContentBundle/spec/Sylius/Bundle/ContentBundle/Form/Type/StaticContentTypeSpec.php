<?php

namespace spec\Sylius\Bundle\ContentBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

class StaticContentTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('My\Resource\Model', array('validation_group'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContentBundle\Form\Type\StaticContentType');
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('publishable', null, Argument::type('array'))->willReturn($builder);
        $builder->add('id', 'text', Argument::type('array'))->willReturn($builder);
        $builder->add('parent', null, Argument::type('array'))->willReturn($builder);
        $builder->add('name', 'text', Argument::type('array'))->willReturn($builder);
        $builder->add('locale', 'text', Argument::type('array'))->willReturn($builder);
        $builder->add('title', 'text', Argument::type('array'))->willReturn($builder);
        $builder->add('routes', 'collection', Argument::type('array'))->willReturn($builder);
        $builder->add('menuNodes', 'collection', Argument::type('array'))->willReturn($builder);
        $builder->add('body', 'textarea', Argument::type('array'))->willReturn($builder);
        $builder->add('publishStartDate', null, Argument::type('array'))->willReturn($builder);
        $builder->add('publishEndDate', null, Argument::type('array'))->willReturn($builder);

        $this->buildForm($builder);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_static_content');
    }
}
