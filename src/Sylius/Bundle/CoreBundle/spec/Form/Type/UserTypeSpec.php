<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UserBundle\Form\Type\UserType;
use Sylius\Component\Core\Model\User;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserTypeSpec extends ObjectBehavior
{
    function let(CanonicalizerInterface $canonicalizer)
    {
        $this->beConstructedWith(User::class, ['sylius'], $canonicalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\UserType');
    }

    function it_extends_user_type_from_user_bundle()
    {
        $this->shouldHaveType(UserType::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_user');
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('plainPassword', 'password', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('enabled', 'checkbox', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('authorizationRoles', 'sylius_role_choice', Argument::any())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, []);
    }
}
