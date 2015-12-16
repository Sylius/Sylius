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

class SetupTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('User');
    }

    function it_is_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('username', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('plain_password', 'password', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('email', 'email', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('load_fixtures', 'checkbox', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }
}
