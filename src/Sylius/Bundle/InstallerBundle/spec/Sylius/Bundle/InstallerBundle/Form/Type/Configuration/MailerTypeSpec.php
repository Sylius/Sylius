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

class MailerTypeSpec extends ObjectBehavior
{
    function it_is_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('sylius_mailer_transport', 'choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('sylius_mailer_host', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('sylius_mailer_user', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('sylius_mailer_password', 'password', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }
}
