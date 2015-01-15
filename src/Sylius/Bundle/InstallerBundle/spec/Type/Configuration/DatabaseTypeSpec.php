<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InstallerBundle\Form\Type\Configuration;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;

class DatabaseTypeSpec extends ObjectBehavior
{
    function it_is_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('sylius_database_driver', 'choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('sylius_database_host', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('sylius_database_port', 'integer', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('sylius_database_name', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('sylius_database_user', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('sylius_database_password', 'password', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }
}
