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
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\UserBundle\Form\Type\UserType;
use Sylius\Component\Resource\Metadata\Metadata;
use Sylius\Component\User\Model\User;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @mixin UserType
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class UserTypeSpec extends ObjectBehavior
{
    function let()
    {
        $metadata = Metadata::fromAliasAndConfiguration('sylius.shop_user', ['driver' => null]);

        $this->beConstructedWith(User::class, ['sylius'], $metadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserType::class);
    }

    function it_extends_abstract_resource_type()
    {
        $this->shouldHaveType(AbstractResourceType::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_shop_user');
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('plainPassword', 'password', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('enabled', 'checkbox', Argument::any())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, []);
    }
}
