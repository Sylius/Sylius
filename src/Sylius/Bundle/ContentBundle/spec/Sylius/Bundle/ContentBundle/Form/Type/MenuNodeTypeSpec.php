<?php

namespace spec\Sylius\Bundle\ContentBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

class MenuNodeTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('My\Resource\Model', array('validation_group'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContentBundle\Form\Type\MenuNodeType');
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder ->add('name', 'text', Argument::type('array'))->willReturn($builder);
        $builder ->add('label', 'text', Argument::type('array'))->willReturn($builder);
        $builder ->add('display', null, Argument::type('array'))->willReturn($builder);
        $builder ->add('displayChildren', null, Argument::type('array'))->willReturn($builder);
        $builder ->add('linkType', 'choice', Argument::type('array'))->willReturn($builder);
        $builder ->add('publishable', null, Argument::type('array'))->willReturn($builder);
        $builder ->add('publishStartDate', 'text', Argument::type('array'))->willReturn($builder);
        $builder ->add('publishEndDate', 'text', Argument::type('array'))->willReturn($builder);
        $builder ->add('route', null, Argument::type('array'))->willReturn($builder);
        $builder ->add('content', null,  Argument::type('array'))->willReturn($builder);
        $builder ->add('uri', null, Argument::type('array'))->willReturn($builder);

        $this->buildForm($builder);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_menu_node');
    }
}
