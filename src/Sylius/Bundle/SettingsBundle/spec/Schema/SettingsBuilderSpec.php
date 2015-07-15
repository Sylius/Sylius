<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Schema;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\Transformer\ParameterTransformerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsBuilderSpec extends ObjectBehavior
{
    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Schema\SettingsBuilder');
    }

    public function it_should_implement_settings_builder_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface');
    }

    public function it_should_extend_options_resolver()
    {
        $this->shouldHaveType('Symfony\Component\OptionsResolver\OptionsResolver');
    }

    public function it_should_initialize_transformers_array_by_default()
    {
        $this->getTransformers()->shouldReturn(array());
    }

    public function it_should_set_transformer_for_parameter_by_name(ParameterTransformerInterface $transformer)
    {
        $this->setTransformer('test', $transformer);

        $this->getTransformers()->shouldReturn(array('test' => $transformer));
    }
}
