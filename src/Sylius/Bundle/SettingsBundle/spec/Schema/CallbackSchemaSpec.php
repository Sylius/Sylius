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
use Prophecy\Argument;
use Sylius\Bundle\SettingsBundle\Schema\CallbackSchema;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @mixin CallbackSchema
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CallbackSchemaSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(function (){}, function (){});
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Schema\CallbackSchema');
    }

    function it_implements_schema_interface()
    {
        $this->shouldImplement(SchemaInterface::class);
    }

    function it_uses_callback_to_build_settings(SettingsBuilderInterface $settingsBuilder)
    {
        $this->beConstructedWith(function (SettingsBuilderInterface $settingsBuilder) {
            $settingsBuilder->setDefaults(['foo' => 'bar']);
        }, function (){});

        $settingsBuilder->setDefaults(['foo' => 'bar'])->shouldBeCalled();

        $this->buildSettings($settingsBuilder);
    }

    function it_uses_callback_to_build_form(FormBuilderInterface $formBuilder)
    {
        $this->beConstructedWith(function (){}, function (FormBuilderInterface $formBuilder) {
            $formBuilder->add('bono', 'u2');
        });

        $formBuilder->add('bono', 'u2')->shouldBeCalled();

        $this->buildForm($formBuilder);
    }
}
