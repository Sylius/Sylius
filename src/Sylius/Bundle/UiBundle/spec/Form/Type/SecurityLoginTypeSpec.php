<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UiBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UiBundle\Form\Type\SecurityLoginType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @mixin SecurityLoginType
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SecurityLoginTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UiBundle\Form\Type\SecurityLoginType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_extends_abstract_form_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_form_with_username_and_password_fields(FormBuilderInterface $builder)
    {
        $builder->add('_username', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('_password', 'password', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('_remember_me', 'checkbox', Argument::any())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, []);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_security_login');
    }
}
