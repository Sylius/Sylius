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
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilder;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Sylius\Bundle\SettingsBundle\Transformer\ParameterTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SettingsBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SettingsBuilder::class);
    }

    function it_implements_settings_builder_interface()
    {
        $this->shouldImplement(SettingsBuilderInterface::class);
    }

    function it_is_a_options_resolver()
    {
        $this->shouldHaveType(OptionsResolver::class);
    }

    function it_initializes_transformers_array_by_default()
    {
        $this->getTransformers()->shouldReturn([]);
    }

    function it_sets_transformer_for_parameter_by_name(ParameterTransformerInterface $transformer)
    {
        $this->setTransformer('test', $transformer);

        $this->getTransformers()->shouldReturn(['test' => $transformer]);
    }
}
