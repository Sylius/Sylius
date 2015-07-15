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
class UserRegistrationTypeSpec extends ObjectBehavior
{
    public function let(CanonicalizerInterface $canonicalizer)
    {
        $this->beConstructedWith('Sylius\Component\User\Model\User', array('sylius'), $canonicalizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\Type\UserRegistrationType');
    }

    public function it_extends_abstract_resource_type()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType');
    }

    public function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_user_registration');
    }

    public function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('plainPassword', 'repeated', Argument::any())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, array());
    }
}
