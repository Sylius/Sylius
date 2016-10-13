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
use Sylius\Bundle\CoreBundle\Form\Type\ShopUserType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\UserBundle\Form\Type\UserType;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @mixin ShopUserType
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ShopUserTypeSpec extends ObjectBehavior
{
    function let(MetadataInterface $metadata)
    {
        $this->beConstructedWith(ShopUser::class, ['sylius'], $metadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ShopUserType::class);
    }

    function it_extends_abstract_resource_type()
    {
        $this->shouldHaveType(AbstractResourceType::class);
    }

    function it_extends_base_user_type()
    {
        $this->shouldHaveType(UserType::class);
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('username', TextType::class, Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('email', EmailType::class, Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('plainPassword', PasswordType::class, Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('enabled', CheckboxType::class, Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->remove('username')->shouldBeCalled()->willReturn($builder);
        $builder->remove('email')->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, []);
    }
}
