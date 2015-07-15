<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReportBundle\Form\Type\Renderer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RendererChoiceTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $choices = array(
            'table' => 'Table renderer',
            'chart' => 'Chart renderer',
        );

        $this->beConstructedWith($choices);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReportBundle\Form\Type\Renderer\RendererChoiceType');
    }

    public function it_should_be_abstract_type_object()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    public function it_sets_default_options(OptionsResolverInterface $resolver)
    {
        $choices = array(
            'table' => 'Table renderer',
            'chart' => 'Chart renderer',
        );

        $resolver->setDefaults(array('choices' => $choices))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_has_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    public function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_renderer_choice');
    }
}
