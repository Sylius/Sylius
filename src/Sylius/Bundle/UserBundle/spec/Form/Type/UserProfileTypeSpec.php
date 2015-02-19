<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserProfileTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Sylius\Component\User\Model\User', array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\Type\UserProfileType');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_user_profile');
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('firstName', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('lastName', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, array());
    }
}
