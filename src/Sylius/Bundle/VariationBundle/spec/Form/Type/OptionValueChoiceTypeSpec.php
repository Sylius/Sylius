<?php

namespace spec\Sylius\Bundle\VariationBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OptionValueChoiceTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('varibale_name');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Form\Type\OptionValueChoiceType');
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    public function it_has_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(Argument::withKey('choice_list'))->shouldBeCalled()->willReturn($resolver);

        $resolver->setRequired(array(
            'option',
        ))->shouldBeCalled()->willReturn($resolver);

        $resolver->addAllowedTypes(array(
            'option' => 'Sylius\Component\Variation\Model\OptionInterface',
        ))->shouldBeCalled()->willReturn($resolver);

        $this->setDefaultOptions($resolver);
    }

    public function it_has_a_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_varibale_name_option_value_choice');
    }
}
