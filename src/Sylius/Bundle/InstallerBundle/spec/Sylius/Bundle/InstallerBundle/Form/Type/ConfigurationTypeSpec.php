<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InstallerBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;

class ConfigurationTypeSpec extends ObjectBehavior
{
    function it_is_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('database', 'sylius_configuration_database', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('mailer', 'sylius_configuration_mailer', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('locale', 'sylius_configuration_locale', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('hidden', 'sylius_configuration_hidden')
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }
}
