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
use Prophecy\Argument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TableConfigurationTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReportBundle\Form\Type\Renderer\TableConfigurationType');
    }

    function it_should_be_abstract_type_object()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_renderer_table');
    }

    function it_builds_form_with_template_choice(FormBuilder $builder)
    {
        $builder->add('template', 'choice', Argument::any())->willReturn($builder);

        $this->buildForm($builder, []);
    }
}
